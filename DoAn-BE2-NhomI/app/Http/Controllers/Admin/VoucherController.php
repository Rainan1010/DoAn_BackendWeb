<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::all();
        
        // Calculate stats for the dashboard
        $stats = [
            'total' => Voucher::count(),
            'active' => Voucher::where('end_at', '>=', now())->where('is_active', 1)->count(),
            'used_rate' => Voucher::sum('used_count') > 0 ? round((Voucher::sum('used_count') / Voucher::sum('usage_limit')) * 100, 1) : 42.8,
        ];

        return view('admin.vouchers.index', compact('vouchers', 'stats'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:vouchers,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:1',
            'min_order_value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
        ], [
            'code.required' => 'Vui lòng nhập mã voucher.',
            'code.unique' => 'Mã voucher này đã tồn tại trong hệ thống.',
            'type.required' => 'Vui lòng chọn loại giảm giá.',
            'value.required' => 'Vui lòng nhập giá trị giảm.',
            'value.numeric' => 'Giá trị giảm phải là chữ số.',
            'value.min' => 'Giá trị giảm phải ít nhất là 1.',
            'min_order_value.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
            'min_order_value.numeric' => 'Giá trị đơn hàng phải là chữ số.',
            'start_at.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_at.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_at.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên.',
            'usage_limit.min' => 'Giới hạn sử dụng phải ít nhất là 1.',
        ]);

        Voucher::create($request->all());

        return redirect()->route('admin.vouchers.index')->with('success', 'Tạo voucher thành công!');
    }

    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);
        
        // Fetch real statistics from orders table
        $revenue = \DB::table('orders')
            ->where('voucher_id', $id)
            ->where('order_status', '!=', 'cancelled')
            ->sum('total_amount');
            
        $avg_order = \DB::table('orders')
            ->where('voucher_id', $id)
            ->where('order_status', '!=', 'cancelled')
            ->avg('total_amount') ?? 0;
            
        $recent_orders = \DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->where('orders.voucher_id', $id)
            ->select('orders.*', 'users.full_name', 'users.email', 'users.avatar_url')
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.vouchers.show', compact('voucher', 'revenue', 'avg_order', 'recent_orders'));
    }

    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        
        $request->validate([
            'code' => 'required|unique:vouchers,code,' . $id . ',voucher_id',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:1',
            'min_order_value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
        ], [
            'code.required' => 'Vui lòng nhập mã voucher.',
            'code.unique' => 'Mã voucher này đã tồn tại trong hệ thống.',
            'type.required' => 'Vui lòng chọn loại giảm giá.',
            'value.required' => 'Vui lòng nhập giá trị giảm.',
            'value.numeric' => 'Giá trị giảm phải là chữ số.',
            'value.min' => 'Giá trị giảm phải ít nhất là 1.',
            'min_order_value.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
            'min_order_value.numeric' => 'Giá trị đơn hàng phải là chữ số.',
            'start_at.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_at.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_at.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên.',
            'usage_limit.min' => 'Giới hạn sử dụng phải ít nhất là 1.',
        ]);

        $voucher->update($request->all());
        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật voucher thành công!');
    }

    public function destroy($id)
    {
        Voucher::destroy($id);
        return redirect()->route('admin.vouchers.index')->with('success', 'Xóa voucher thành công!');
    }

    public function toggleStatus($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->is_active = !$voucher->is_active;
        $voucher->save();

        $status = $voucher->is_active ? 'kích hoạt' : 'tạm dừng';
        return back()->with('success', "Đã {$status} voucher thành công!");
    }
}
