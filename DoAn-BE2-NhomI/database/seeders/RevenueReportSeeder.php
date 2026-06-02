<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\RevenueReport;

class RevenueReportSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | XÓA DỮ LIỆU CŨ
        |--------------------------------------------------------------------------
        */

        RevenueReport::truncate();

        /*
        |--------------------------------------------------------------------------
        | TẠO 30 NGÀY DỮ LIỆU
        |--------------------------------------------------------------------------
        */

        for ($i = 30; $i >= 0; $i--) {

            /*
            |--------------------------------------------------------------------------
            | RANDOM DỮ LIỆU
            |--------------------------------------------------------------------------
            */

            $totalOrders =
                rand(5, 30);

            $totalItemsSold =
                rand(10, 80);

            $totalRevenue =
                rand(20000000, 200000000);

            $avgOrderValue =
                $totalRevenue / $totalOrders;

            /*
            |--------------------------------------------------------------------------
            | INSERT
            |--------------------------------------------------------------------------
            */

            RevenueReport::create([

                'report_date' => now()
                    ->subDays($i)
                    ->format('Y-m-d'),

                'total_revenue' => $totalRevenue,

                'total_orders' => $totalOrders,

                'total_items_sold' => $totalItemsSold,

                'avg_order_value' => $avgOrderValue,
            ]);
        }
    }
}