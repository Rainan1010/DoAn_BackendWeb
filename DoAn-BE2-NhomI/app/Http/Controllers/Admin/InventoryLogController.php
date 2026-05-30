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
            $keyword = $request->search;

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

        return view('admin.inventory_logs.create', compact('variants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,variant_id',
            'quantity' => 'required|integer|min:1',
            'import_price' => 'nullable|numeric|min:0',
            'supplier_name' => 'nullable|string|max:255',
            'reference_code' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ], [
            'variant_id.required' => 'Vui lòng chọn sản phẩm cần nhập kho.',
            'variant_id.exists' => 'Sản phẩm không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng nhập kho.',
            'quantity.integer' => 'Số lượng nhập kho phải là số nguyên.',
            'quantity.min' => 'Số lượng nhập kho phải lớn hơn 0.',
        ]);

        DB::beginTransaction();

        try {
            $variant = DB::table('product_variants')
                ->where('variant_id', $request->variant_id)
                ->lockForUpdate()
                ->first();

            if (!$variant) {
                return back()
                    ->withInput()
                    ->with('error', 'Không tìm thấy sản phẩm cần nhập kho.');
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
                $noteParts[] = 'Giá nhập: ' . number_format($request->import_price, 0, ',', '.') . 'đ';
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
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Nhập kho thất bại: ' . $e->getMessage());
        }
    }
}