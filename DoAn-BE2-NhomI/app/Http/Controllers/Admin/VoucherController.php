<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    private const VOUCHER_NOT_FOUND_MESSAGE = 'Mã giảm giá này không còn tồn tại hoặc đã bị người khác xóa. Vui lòng tải lại danh sách.';

    private function findVoucher($id): ?Voucher
    {
        return Voucher::find($id);
    }

    private function voucherNotFoundRedirect()
    {
        return redirect()
            ->route('admin.vouchers.index')
            ->with('error', self::VOUCHER_NOT_FOUND_MESSAGE);
    }

    public function index()
    {
        $vouchers = Voucher::orderByDesc('voucher_id')->get();

        $totalUsed = (int) $vouchers->sum('used_count');
        $totalLimit = (int) $vouchers->sum('usage_limit');
        $usedRate = $totalLimit > 0 ? round(($totalUsed / $totalLimit) * 100, 1) : 0;

        $now = now();
        $lastMonth = $now->copy()->subMonth();

        $ordersThisMonth = DB::table('orders')
            ->whereNotNull('voucher_id')
            ->where('order_status', '!=', 'cancelled')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        $ordersLastMonth = DB::table('orders')
            ->whereNotNull('voucher_id')
            ->where('order_status', '!=', 'cancelled')
            ->whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        if ($ordersLastMonth > 0) {
            $monthGrowth = round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1);
        } else {
            $monthGrowth = $ordersThisMonth > 0 ? 100 : 0;
        }

        $stats = [
            'total' => $vouchers->count(),
            'active' => $vouchers->filter(fn ($v) => $this->isVoucherActive($v))->count(),
            'used_rate' => $usedRate,
            'month_growth' => $monthGrowth,
            'total_redeemed' => $totalUsed,
            'orders_with_voucher' => DB::table('orders')
                ->whereNotNull('voucher_id')
                ->where('order_status', '!=', 'cancelled')
                ->count(),
        ];

        $recentRedeemers = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->whereNotNull('orders.voucher_id')
            ->where('orders.order_status', '!=', 'cancelled')
            ->select('users.avatar_url', 'users.full_name')
            ->orderByDesc('orders.created_at')
            ->limit(3)
            ->get();

        return view('admin.vouchers.index', compact('vouchers', 'stats', 'recentRedeemers'));
    }

    public function create()
    {
        $nextVoucherId = (int) (Voucher::max('voucher_id') ?? 0) + 1;

        return view('admin.vouchers.create', compact('nextVoucherId'));
    }

    private function isVoucherActive(Voucher $voucher): bool
    {
        if (!$voucher->is_active) {
            return false;
        }

        if ($voucher->start_at && now()->lt(Carbon::parse($voucher->start_at))) {
            return false;
        }

        if ($voucher->end_at && now()->gt(Carbon::parse($voucher->end_at))) {
            return false;
        }

        return true;
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

        return redirect()->route('admin.vouchers.index')->with('success', 'Tạo mã giảm giá thành công!');
    }

    public function show($id)
    {
        $voucher = $this->findVoucher($id);
        if (!$voucher) {
            return $this->voucherNotFoundRedirect();
        }
        
        $revenue = (float) DB::table('orders')
            ->where('voucher_id', $id)
            ->where('order_status', '!=', 'cancelled')
            ->sum('total_amount');

        $avg_order = (float) (DB::table('orders')
            ->where('voucher_id', $id)
            ->where('order_status', '!=', 'cancelled')
            ->avg('total_amount') ?? 0);

        $recent_orders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.user_id')
            ->where('orders.voucher_id', $id)
            ->select('orders.*', 'users.full_name', 'users.email', 'users.avatar_url')
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();

        $orderStats = DB::table('orders')
            ->where('voucher_id', $id)
            ->selectRaw("COUNT(*) as total_orders, SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders, COALESCE(SUM(discount_amount), 0) as total_discount")
            ->first();

        $totalOrders = (int) ($orderStats->total_orders ?? 0);
        $cancelledOrders = (int) ($orderStats->cancelled_orders ?? 0);
        $totalDiscount = (float) ($orderStats->total_discount ?? 0);

        $usageRate = ($voucher->usage_limit ?? 0) > 0
            ? round(($voucher->used_count / $voucher->usage_limit) * 100, 1)
            : ($totalOrders > 0 ? 100 : 0);

        $cancelRate = $totalOrders > 0
            ? round(($cancelledOrders / $totalOrders) * 100, 1)
            : 0;

        $estimatedProfit = $revenue - $totalDiscount;

        $revenuePreviousMonth = (float) DB::table('orders')
            ->where('voucher_id', $id)
            ->where('order_status', '!=', 'cancelled')
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->sum('total_amount');

        $revenueGrowth = $revenuePreviousMonth > 0
            ? round((($revenue - $revenuePreviousMonth) / $revenuePreviousMonth) * 100, 1)
            : ($revenue > 0 ? 100 : 0);

        $weeklyRevenue = DB::table('orders')
            ->where('voucher_id', $id)
            ->where('order_status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as day, COALESCE(SUM(total_amount), 0) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $chartDays = collect(range(6, 0))->map(function ($daysAgo) use ($weeklyRevenue) {
            $day = now()->subDays($daysAgo)->format('Y-m-d');

            return [
                'label' => now()->subDays($daysAgo)->format('d/m'),
                'total' => (float) ($weeklyRevenue[$day] ?? 0),
            ];
        });

        $chartMax = max($chartDays->max('total'), 1);

        return view('admin.vouchers.show', compact(
            'voucher',
            'revenue',
            'avg_order',
            'recent_orders',
            'usageRate',
            'cancelRate',
            'estimatedProfit',
            'revenueGrowth',
            'chartDays',
            'chartMax',
            'totalDiscount'
        ));
    }

    public function edit($id)
    {
        $voucher = $this->findVoucher($id);
        if (!$voucher) {
            return $this->voucherNotFoundRedirect();
        }
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        $voucher = $this->findVoucher($id);
        if (!$voucher) {
            return $this->voucherNotFoundRedirect();
        }
        
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
        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    public function destroy($id)
    {
        $voucher = $this->findVoucher($id);
        if (!$voucher) {
            return $this->voucherNotFoundRedirect();
        }
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Xóa mã giảm giá thành công!');
    }

    public function toggleStatus($id)
    {
        $voucher = $this->findVoucher($id);
        if (!$voucher) {
            return $this->voucherNotFoundRedirect();
        }
        $voucher->is_active = !$voucher->is_active;
        $voucher->save();

        $status = $voucher->is_active ? 'kích hoạt' : 'tạm dừng';
        return back()->with('success', "Đã {$status} mã giảm giá thành công!");
    }
}
