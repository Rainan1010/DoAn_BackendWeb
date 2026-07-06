<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;


class VoucherController extends Controller
{
    // Message constants
    private const NOT_FOUND = 'Voucher không tồn tại hoặc đã bị xóa. Vui lòng tải lại danh sách.';
    private const CONFLICT = '⚠️ Xung đột dữ liệu: Voucher này đã được người khác chỉnh sửa. Vui lòng tải lại trang để xem thông tin mới nhất.';

    
    public function index()
    {
        // Retrieve all vouchers
        $vouchers = Voucher::all();

        // Statistics for dashboard cards
        $total = $vouchers->count();
        $active = $vouchers->where('is_active', true)->count();
        // Calculate usage rate percentage (total used_count / total usage_limit) if applicable
        $totalUsed = $vouchers->sum('used_count');
        $totalLimit = $vouchers->sum(function ($v) {
            return $v->usage_limit ?? 0;
        });
        $usedRate = $totalLimit > 0 ? round(($totalUsed / $totalLimit) * 100) : 0;

        $stats = [
            'total' => $total,
            'active' => $active,
            'used_rate' => $usedRate,
        ];

        return view('admin.vouchers.index', compact('vouchers', 'stats'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $maxValue = $request->input('type') === 'percent' ? 100 : 99999999.99;

        $validated = $request->validate([
            'code'            => 'required|string|max:50|unique:vouchers,code',
            'type'            => 'required|in:percent,fixed',
            'value'           => "required|numeric|min:0|max:$maxValue",
            'min_order_value' => 'required|numeric|min:0|max:999999999999999',
            'max_discount'    => 'nullable|numeric|min:0|max:999999999999999',
            'usage_limit'     => 'nullable|integer|min:0|max:2147483647',
            'start_at'        => 'required|date|after_or_equal:1000-01-01|before_or_equal:9999-12-31 23:59:59',
            'end_at'          => 'required|date|after:start_at|before_or_equal:9999-12-31 23:59:59',
            'is_active'       => 'sometimes|boolean',
        ], [
            'code.required'            => 'Vui lòng nhập mã voucher.',
            'code.string'              => 'Mã voucher phải là chuỗi ký tự.',
            'code.max'                 => 'Mã voucher không được vượt quá 50 ký tự.',
            'code.unique'              => 'Mã voucher đã tồn tại trên hệ thống.',
            'type.required'            => 'Vui lòng chọn loại voucher.',
            'type.in'                  => 'Loại voucher không hợp lệ (chỉ chấp nhận: phần trăm hoặc cố định).',
            'value.required'           => 'Vui lòng nhập giá trị giảm.',
            'value.numeric'            => 'Giá trị giảm phải là số.',
            'value.min'                => 'Giá trị giảm không được nhỏ hơn 0.',
            'value.max'                => $request->input('type') === 'percent' 
                                            ? 'Giá trị giảm theo phần trăm không được vượt quá 100%.' 
                                            : 'Giá trị giảm cố định không được vượt quá 99.999.999,99 đ.',
            'min_order_value.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
            'min_order_value.numeric'  => 'Giá trị đơn hàng tối thiểu phải là số.',
            'min_order_value.min'      => 'Giá trị đơn hàng tối thiểu không được nhỏ hơn 0.',
            'min_order_value.max'      => 'Giá trị đơn hàng tối thiểu không được vượt quá 999.999.999.999.999 đ.',
            'max_discount.numeric'     => 'Mức giảm tối đa phải là số.',
            'max_discount.min'         => 'Mức giảm tối đa không được nhỏ hơn 0.',
            'max_discount.max'         => 'Mức giảm tối đa không được vượt quá 999.999.999.999.999 đ.',
            'usage_limit.integer'      => 'Giới hạn sử dụng phải là số nguyên.',
            'usage_limit.min'          => 'Giới hạn sử dụng không được nhỏ hơn 0.',
            'usage_limit.max'          => 'Giới hạn sử dụng không được vượt quá 2.147.483.647 lượt.',
            'start_at.required'        => 'Vui lòng chọn thời gian bắt đầu.',
            'start_at.date'            => 'Thời gian bắt đầu không hợp lệ.',
            'start_at.after_or_equal'  => 'Thời gian bắt đầu không được trước năm 1000.',
            'start_at.before_or_equal' => 'Năm bắt đầu chỉ cho phép nhập tối đa 4 số (không vượt quá năm 9999).',
            'end_at.required'          => 'Vui lòng chọn thời gian kết thúc.',
            'end_at.date'              => 'Thời gian kết thúc không hợp lệ.',
            'end_at.after'             => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'end_at.before_or_equal'   => 'Năm kết thúc chỉ cho phép nhập tối đa 4 số (không vượt quá năm 9999).',
            'is_active.boolean'        => 'Trạng thái hoạt động không hợp lệ.',
        ]);

        $voucher = Voucher::create($validated);
        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Thêm voucher mới thành công!');
    }

    public function edit(string $id)
    {
        $voucher = Voucher::find($id);
        if (! $voucher) {
            return redirect()->route('admin.vouchers.index')
                ->with('error', self::NOT_FOUND);
        }
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Display voucher details with additional stats.
     */
    public function show(string $id)
    {
        $voucher = Voucher::find($id);
        if (! $voucher) {
            return redirect()->route('admin.vouchers.index')
                ->with('error', self::NOT_FOUND);
        }

        // Placeholder statistics – replace with real calculations later
        $revenue = 0;
        $avg_order = 0;
        $recent_orders = [];

        return view('admin.vouchers.show', compact('voucher', 'revenue', 'avg_order', 'recent_orders'));
    }

    /**
     * Toggle the active status of a voucher.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            return redirect()->route('admin.vouchers.index')
                ->with('error', self::NOT_FOUND);
        }

        // Optimistic concurrency check
        $lastUpdated = $request->input('last_updated_at');
        if ($lastUpdated && $voucher->updated_at && $voucher->updated_at->format('Y-m-d H:i:s') !== $lastUpdated) {
            return redirect()->back()
                ->with('error', self::CONFLICT);
        }

        $voucher->is_active = !$voucher->is_active;
        $voucher->save();

        $statusText = $voucher->is_active ? 'kích hoạt' : 'tạm dừng';
        return redirect()->back()
            ->with('success', "Đã chuyển đổi trạng thái voucher sang {$statusText} thành công!");
    }

    public function update(Request $request, string $id)
    {
        $voucher = Voucher::find($id);
        if (! $voucher) {
            return redirect()->route('admin.vouchers.index')
                ->with('error', self::NOT_FOUND);
        }

        // Optimistic concurrency check
        $lastUpdated = $request->input('last_updated_at');
        if ($lastUpdated && $voucher->updated_at && $voucher->updated_at->format('Y-m-d H:i:s') !== $lastUpdated) {
            return redirect()->back()
                ->withInput()
                ->with('error', self::CONFLICT);
        }

        $targetType = $request->input('type', $voucher->type);
        $maxValue = $targetType === 'percent' ? 100 : 99999999.99;

        $validated = $request->validate([
            'code'            => "sometimes|string|max:50|unique:vouchers,code,$id,voucher_id",
            'type'            => 'sometimes|in:percent,fixed',
            'value'           => "sometimes|numeric|min:0|max:$maxValue",
            'min_order_value' => 'sometimes|numeric|min:0|max:999999999999999',
            'max_discount'    => 'nullable|numeric|min:0|max:999999999999999',
            'usage_limit'     => 'nullable|integer|min:0|max:2147483647',
            'start_at'        => 'sometimes|date|after_or_equal:1000-01-01|before_or_equal:9999-12-31 23:59:59',
            'end_at'          => 'sometimes|date|after:start_at|before_or_equal:9999-12-31 23:59:59',
            'is_active'       => 'sometimes|boolean',
        ], [
            'code.string'              => 'Mã voucher phải là chuỗi ký tự.',
            'code.max'                 => 'Mã voucher không được vượt quá 50 ký tự.',
            'code.unique'              => 'Mã voucher đã tồn tại trên hệ thống.',
            'type.in'                  => 'Loại voucher không hợp lệ (chỉ chấp nhận: phần trăm hoặc cố định).',
            'value.numeric'            => 'Giá trị giảm phải là số.',
            'value.min'                => 'Giá trị giảm không được nhỏ hơn 0.',
            'value.max'                => $targetType === 'percent' 
                                            ? 'Giá trị giảm theo phần trăm không được vượt quá 100%.' 
                                            : 'Giá trị giảm cố định không được vượt quá 99.999.999,99 đ.',
            'min_order_value.numeric'  => 'Giá trị đơn hàng tối thiểu phải là số.',
            'min_order_value.min'      => 'Giá trị đơn hàng tối thiểu không được nhỏ hơn 0.',
            'min_order_value.max'      => 'Giá trị đơn hàng tối thiểu không được vượt quá 999.999.999.999.999 đ.',
            'max_discount.numeric'     => 'Mức giảm tối đa phải là số.',
            'max_discount.min'         => 'Mức giảm tối đa không được nhỏ hơn 0.',
            'max_discount.max'         => 'Mức giảm tối đa không được vượt quá 999.999.999.999.999 đ.',
            'usage_limit.integer'      => 'Giới hạn sử dụng phải là số nguyên.',
            'usage_limit.min'          => 'Giới hạn sử dụng không được nhỏ hơn 0.',
            'usage_limit.max'          => 'Giới hạn sử dụng không được vượt quá 2.147.483.647 lượt.',
            'start_at.date'            => 'Thời gian bắt đầu không hợp lệ.',
            'start_at.after_or_equal'  => 'Thời gian bắt đầu không được trước năm 1000.',
            'start_at.before_or_equal' => 'Năm bắt đầu chỉ cho phép nhập tối đa 4 số (không vượt quá năm 9999).',
            'end_at.date'              => 'Thời gian kết thúc không hợp lệ.',
            'end_at.after'             => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'end_at.before_or_equal'   => 'Năm kết thúc chỉ cho phép nhập tối đa 4 số (không vượt quá năm 9999).',
            'is_active.boolean'        => 'Trạng thái hoạt động không hợp lệ.',
        ]);

        $voucher->update($validated);
        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Cập nhật voucher thành công!');
    }

    public function destroy(string $id, Request $request)
    {
        $voucher = Voucher::find($id);
        if (! $voucher) {
            return redirect()->route('admin.vouchers.index')
                ->with('error', self::NOT_FOUND);
        }

        // Concurrency check for delete
        $lastUpdated = $request->input('last_updated_at');
        if ($lastUpdated && $voucher->updated_at && $voucher->updated_at->format('Y-m-d H:i:s') !== $lastUpdated) {
            return redirect()->back()
                ->with('error', self::CONFLICT);
        }

        $voucher->delete();
        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Xóa voucher thành công!');
    }
}
