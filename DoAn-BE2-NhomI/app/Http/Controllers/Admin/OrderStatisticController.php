<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderStatisticController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | KPI
        |--------------------------------------------------------------------------
        */

        $totalOrders = DB::table('orders')->count();

        // Vì DB của bạn có cả pending và confirmed
        $pendingOrders = DB::table('orders')
            ->whereIn('order_status', ['pending', 'confirmed'])
            ->count();

        $completedOrders = DB::table('orders')
            ->whereIn('order_status', ['completed', 'delivered'])
            ->count();

        $cancelledOrders = DB::table('orders')
            ->whereIn('order_status', ['cancelled', 'canceled'])
            ->count();

        $totalRevenue = DB::table('orders')
            ->whereIn('order_status', ['completed', 'delivered', 'shipping', 'processing'])
            ->sum('total_amount');

        $todayRevenue = DB::table('orders')
            ->whereDate('created_at', today())
            ->whereIn('order_status', ['completed', 'delivered', 'shipping', 'processing'])
            ->sum('total_amount');

        $todayNewOrders = DB::table('orders')
            ->whereDate('created_at', today())
            ->count();

        /*
        |--------------------------------------------------------------------------
        | QUERY DANH SÁCH ĐƠN HÀNG
        |--------------------------------------------------------------------------
        */

        $recentOrdersQuery = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.user_id')
            ->select(
                'orders.order_id',
                'orders.user_id',
                'orders.shipping_address_id',
                'orders.voucher_id',
                'orders.order_code',
                'orders.subtotal',
                'orders.shipping_fee',
                'orders.discount_amount',
                'orders.total_amount',
                'orders.payment_method',
                'orders.payment_status',
                'orders.order_status',
                'orders.cancel_reason',
                'orders.paid_at',
                'orders.created_at',
                'users.full_name as customer_name',
                'users.email as customer_email'
            );

        if ($request->filled('search')) {
            $keyword = $request->search;

            $recentOrdersQuery->where(function ($query) use ($keyword) {
                $query->where('orders.order_code', 'like', '%' . $keyword . '%')
                    ->orWhere('users.full_name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.email', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->filled('status')) {
            $recentOrdersQuery->where('orders.order_status', $request->status);
        }

        // Nếu muốn giống thứ tự trong phpMyAdmin thì dùng order_id ASC
        $recentOrders = $recentOrdersQuery
            ->orderBy('orders.order_id', 'asc')
            ->limit(10)
            ->get();

        return view('admin.order_statistics.index', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'cancelledOrders',
            'totalRevenue',
            'todayRevenue',
            'todayNewOrders',
            'recentOrders'
        ));
    }
}