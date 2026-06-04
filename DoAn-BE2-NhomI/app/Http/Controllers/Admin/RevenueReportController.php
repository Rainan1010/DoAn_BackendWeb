<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RevenueReport;

class RevenueReportController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        $from =
            request('from')
            ?? now()->subDays(5)->format('Y-m-d');

        $to =
            request('to')
            ?? now()->format('Y-m-d');
        if ($from > now()->toDateString()) {

            return redirect()
                ->route('admin.revenue_reports.index')
                ->with(
                    'error',
                    'Ngày bắt đầu không được lớn hơn ngày hiện tại'
                );
        }

        if ($to > now()->toDateString()) {

            return redirect()
                ->route('admin.revenue_reports.index')
                ->with(
                    'error',
                    'Ngày kết thúc không được lớn hơn ngày hiện tại'
                );
        }

        if ($to < $from) {

            return redirect()
                ->route('admin.revenue_reports.index')
                ->with(
                    'error',
                    'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu'
                );
        }
        /*
        |--------------------------------------------------------------------------
        | QUERY ĐƠN ĐÃ THANH TOÁN
        |--------------------------------------------------------------------------
        */

        $query =

            Order::where(
                'payment_status',
                'paid'
            )

                ->whereDate(
                    'created_at',
                    '>=',
                    $from
                )

                ->whereDate(
                    'created_at',
                    '<=',
                    $to
                );

        /*
        |--------------------------------------------------------------------------
        | TỔNG DOANH THU THEO BỘ LỌC
        |--------------------------------------------------------------------------
        */

        $totalRevenue =

            $query->sum(
                'total_amount'
            );
        /*
  |--------------------------------------------------------------------------
  | TỔNG TOÀN BỘ DOANH THU
  |--------------------------------------------------------------------------
  */
        $totalRevenueAll =

            Order::where(
                'payment_status',
                'paid'
            )

                ->sum(
                    'total_amount'
                );
        /*
        |--------------------------------------------------------------------------
        |  GIÁ TRỊ TRUNG BÌNH CỦA TOÀN HỆ THỐNG
        |--------------------------------------------------------------------------
        */
        $avgOrderValueAll =

            Order::where(
                'payment_status',
                'paid'
            )

                ->avg(
                    'total_amount'
                );
        /*
        |--------------------------------------------------------------------------
        | TỔNG ĐƠN HÀNG
        |--------------------------------------------------------------------------
        */

        $totalOrders =

            $query->count();

        /*
        |--------------------------------------------------------------------------
        | TỔNG SẢN PHẨM ĐÃ BÁN
        |--------------------------------------------------------------------------
        */

        $totalItemsSold =

            OrderItem::whereHas(

                'order',

                function ($q) use ($from, $to) {

                    $q->where(
                        'payment_status',
                        'paid'
                    )

                        ->whereDate(
                            'created_at',
                            '>=',
                            $from
                        )

                        ->whereDate(
                            'created_at',
                            '<=',
                            $to
                        );
                }
            )

                ->sum(
                    'quantity'
                );

        /*
        |--------------------------------------------------------------------------
        | GIÁ TRỊ ĐƠN HÀNG TRUNG BÌNH
        |--------------------------------------------------------------------------
        */

        $avgOrderValue =

            $query->avg(
                'total_amount'
            );

        /*
        |--------------------------------------------------------------------------
        | DỮ LIỆU BIỂU ĐỒ
        |--------------------------------------------------------------------------
        */

        $chartData = RevenueReport::whereBetween(
            'report_date',
            [$from, $to]
        )
            ->orderBy('report_date')
            ->get()
            ->map(function ($item) {

                return [

                    'date' =>
                        \Carbon\Carbon::parse(
                            $item->report_date
                        )->format('d/m'),

                    'revenue' =>
                        (int) $item->total_revenue
                ];
            });
        /*
        |--------------------------------------------------------------------------
        | DANH SÁCH REPORT
        |--------------------------------------------------------------------------
        */

        $reports = RevenueReport::whereBetween(
            'report_date',
            [$from, $to]
        )
            ->latest('report_date')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(

            'admin.revenue_reports.index',

            compact(

                'totalRevenueAll',

                'totalRevenue',

                'totalOrders',

                'totalItemsSold',

                'avgOrderValue',

                'avgOrderValueAll',

                'chartData',

                'reports',

                'from',

                'to'
            )
        );
    }
}