<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryLogController extends Controller
{
    public function index(Request $request)
    {
        $totalStock = DB::table('product_variants')
            ->sum('stock_quantity');

        $lowStockProducts = DB::table('product_variants')
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 5)
            ->count();

        $outOfStockProducts = DB::table('product_variants')
            ->where('stock_quantity', '<=', 0)
            ->count();

        $logsQuery = DB::table('inventory_logs')
            ->leftJoin('product_variants', 'inventory_logs.variant_id', '=', 'product_variants.variant_id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.product_id')
            ->leftJoin('orders', 'inventory_logs.order_id', '=', 'orders.order_id')
            ->leftJoin('users', 'inventory_logs.user_id', '=', 'users.user_id')
            ->select(
                'inventory_logs.log_id',
                'inventory_logs.variant_id',
                'inventory_logs.order_id',
                'inventory_logs.user_id',
                'inventory_logs.action_type',
                'inventory_logs.quantity_change',
                'inventory_logs.stock_after',
                'inventory_logs.note',
                'inventory_logs.created_at',
                'inventory_logs.updated_at',
                'product_variants.sku',
                'products.name as product_name',
                'orders.order_code',
                'users.full_name as user_name'
            );

        if ($request->filled('action_type')) {
            $logsQuery->where('inventory_logs.action_type', $request->action_type);
        }

        if ($request->filled('search')) {
            $keyword = $this->normalizeText($request->search);

            $logsQuery->where(function ($query) use ($keyword) {
                $query->where('product_variants.sku', 'like', '%' . $keyword . '%')
                    ->orWhere('products.name', 'like', '%' . $keyword . '%')
                    ->orWhere('orders.order_code', 'like', '%' . $keyword . '%')
                    ->orWhere('inventory_logs.note', 'like', '%' . $keyword . '%');
            });
        }

        $logs = $logsQuery
            ->orderByDesc('inventory_logs.created_at')
            ->paginate(10)
            ->withQueryString();

        $todayChange = DB::table('inventory_logs')
            ->whereDate('created_at', today())
            ->sum('quantity_change');

        $todayLogs = DB::table('inventory_logs')
            ->whereDate('created_at', today())
            ->count();

        return view('admin.inventory_logs.index', compact(
            'totalStock',
            'lowStockProducts',
            'outOfStockProducts',
            'logs',
            'todayChange',
            'todayLogs'
        ));
    }

    public function create()
    {
        $variants = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.product_id')
            ->leftJoin('product_images', function ($join) {
                $join->on('product_images.product_id', '=', 'products.product_id')
                    ->where('product_images.is_primary', 1);
            })
            ->select(
                'product_variants.variant_id',
                'product_variants.product_id',
                'product_variants.sku',
                'product_variants.price',
                'product_variants.sale_price',
                'product_variants.stock_quantity',
                'product_variants.attribute_values',
                'products.name as product_name',
                'product_images.image_url'
            )
            ->where('product_variants.is_active', 1)
            ->orderBy('products.name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Token chống submit trùng
        |--------------------------------------------------------------------------
        | Mỗi lần mở form nhập kho sẽ sinh một token.
        | Khi store xử lý xong token sẽ bị xóa khỏi session.
        | Nếu người dùng bấm submit nhiều lần, request sau sẽ bị chặn.
        */
        $importToken = bin2hex(random_bytes(16));

        $tokens = session('inventory_import_tokens', []);
        $tokens[$importToken] = true;

        session(['inventory_import_tokens' => $tokens]);

        return view('admin.inventory_logs.create', compact('variants', 'importToken'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'variant_id' => $this->normalizeNumber($request->variant_id),
            'quantity' => $this->normalizeNumber($request->quantity),
            'import_price' => $this->normalizeMoney($request->import_price),
            'supplier_name' => $this->normalizeText($request->supplier_name),
            'reference_code' => $this->normalizeText($request->reference_code),
            'note' => $this->normalizeText($request->note),
        ]);

        $request->validate([
            '_form_token' => 'required|string',
            'variant_id' => 'required|integer|exists:product_variants,variant_id',
            'quantity' => 'required|integer|min:1|max:10000',
            'import_price' => 'nullable|numeric|min:0|max:999999999999',
            'supplier_name' => 'nullable|string|max:255',
            'reference_code' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:1000',
        ], [
            '_form_token.required' => 'Phiên nhập kho không hợp lệ. Vui lòng tải lại trang.',
            'variant_id.required' => 'Vui lòng chọn sản phẩm cần nhập kho.',
            'variant_id.integer' => 'Sản phẩm nhập kho không hợp lệ.',
            'variant_id.exists' => 'Sản phẩm không tồn tại hoặc đã bị xóa.',
            'quantity.required' => 'Vui lòng nhập số lượng nhập kho.',
            'quantity.integer' => 'Số lượng nhập kho phải là số nguyên.',
            'quantity.min' => 'Số lượng nhập kho phải lớn hơn 0.',
            'quantity.max' => 'Số lượng nhập kho quá lớn.',
            'import_price.numeric' => 'Giá nhập phải là số.',
            'import_price.min' => 'Giá nhập không được âm.',
            'import_price.max' => 'Giá nhập quá lớn.',
            'supplier_name.max' => 'Tên nhà cung cấp không được vượt quá 255 ký tự.',
            'reference_code.max' => 'Mã hóa đơn tham chiếu không được vượt quá 100 ký tự.',
            'note.max' => 'Ghi chú không được vượt quá 1000 ký tự.',
        ]);

        $tokens = session('inventory_import_tokens', []);
        $formToken = $request->input('_form_token');

        if (!isset($tokens[$formToken])) {
            return redirect()
                ->route('admin.inventory-logs.create')
                ->with('error', 'Yêu cầu nhập kho đã được xử lý hoặc không hợp lệ. Vui lòng tạo phiếu nhập kho mới.');
        }

        unset($tokens[$formToken]);
        session(['inventory_import_tokens' => $tokens]);

        DB::beginTransaction();

        try {
            $variant = DB::table('product_variants')
                ->where('variant_id', $request->variant_id)
                ->where('is_active', 1)
                ->lockForUpdate()
                ->first();

            if (!$variant) {
                DB::rollBack();

                return redirect()
                    ->route('admin.inventory-logs.create')
                    ->withInput()
                    ->with('error', 'Không tìm thấy sản phẩm cần nhập kho hoặc sản phẩm đã bị ẩn.');
            }

            $oldStock = (int) $variant->stock_quantity;
            $quantityImport = (int) $request->quantity;
            $newStock = $oldStock + $quantityImport;

            DB::table('product_variants')
                ->where('variant_id', $variant->variant_id)
                ->update([
                    'stock_quantity' => $newStock,
                    'updated_at' => now(),
                ]);

            $noteParts = [];

            if ($request->filled('supplier_name')) {
                $noteParts[] = 'Nhà cung cấp: ' . $request->supplier_name;
            }

            if ($request->filled('reference_code')) {
                $noteParts[] = 'Mã hóa đơn: ' . $request->reference_code;
            }

            if ($request->filled('import_price')) {
                $noteParts[] = 'Giá nhập: ' . number_format((float) $request->import_price, 0, ',', '.') . 'đ';
            }

            if ($request->filled('note')) {
                $noteParts[] = $request->note;
            }

            $finalNote = count($noteParts) > 0
                ? implode(' | ', $noteParts)
                : 'Nhập kho thủ công từ admin';

            DB::table('inventory_logs')->insert([
                'variant_id' => $variant->variant_id,
                'order_id' => null,
                'user_id' => Auth::id(),
                'action_type' => 'import',
                'quantity_change' => $quantityImport,
                'stock_after' => $newStock,
                'note' => $finalNote,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.inventory-logs.index')
                ->with('success', 'Nhập kho thành công. Tồn kho đã được cập nhật.');
        } catch (\Throwable $e) {
            DB::rollBack();

            // Tạo lại token mới để người dùng có thể thử nhập kho lại sau khi lỗi
            $newToken = bin2hex(random_bytes(16));
            $tokens = session('inventory_import_tokens', []);
            $tokens[$newToken] = true;
            session(['inventory_import_tokens' => $tokens]);

            return redirect()
                ->route('admin.inventory-logs.create')
                ->withInput()
                ->with('importToken', $newToken)
                ->with('error', 'Nhập kho thất bại: ' . $e->getMessage());
        }
    }

    private function normalizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (function_exists('mb_convert_kana')) {
            $value = mb_convert_kana($value, 'asKV', 'UTF-8');
        }

        $value = str_replace('　', ' ', $value);
        $value = preg_replace('/^\s+|\s+$/u', '', $value);
        $value = preg_replace('/\s+/u', ' ', $value);

        return $value;
    }

    private function normalizeNumber($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;

        if (function_exists('mb_convert_kana')) {
            $value = mb_convert_kana($value, 'n', 'UTF-8');
        }

        $value = preg_replace('/\s+/u', '', $value);

        return $value;
    }

    private function normalizeMoney($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (string) $value;

        if (function_exists('mb_convert_kana')) {
            $value = mb_convert_kana($value, 'n', 'UTF-8');
        }

        $value = preg_replace('/[^\d]/u', '', $value);

        return $value === '' ? null : $value;
    }
}