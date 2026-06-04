<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('order_items')->truncate();

        $orders = DB::table('orders')->get();

        foreach ($orders as $order) {

            $itemCount = rand(1, 3);

            $usedVariants = [];

            $orderSubtotal = 0;

            for ($i = 0; $i < $itemCount; $i++) {

                $variant = DB::table('product_variants')
                    ->inRandomOrder()
                    ->first();

                if (!$variant) {
                    continue;
                }

                if (in_array(
                    $variant->variant_id,
                    $usedVariants
                )) {
                    continue;
                }

                $usedVariants[] =
                    $variant->variant_id;

                $product = DB::table('products')
                    ->where(
                        'product_id',
                        $variant->product_id
                    )
                    ->first();

                $price =
                    $variant->sale_price
                    ? $variant->sale_price
                    : $variant->price;

                $quantity = rand(1, 3);

                $itemSubtotal =
                    $price * $quantity;

                $orderSubtotal +=
                    $itemSubtotal;

                DB::table('order_items')
                    ->insert([

                        'order_id' =>
                            $order->order_id,

                        'variant_id' =>
                            $variant->variant_id,

                        'product_name' =>
                            $product->name,

                        'variant_info' =>
                            $variant->attribute_values,

                        'unit_price' =>
                            $price,

                        'quantity' =>
                            $quantity,

                        'subtotal' =>
                            $itemSubtotal,
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE ORDER TOTAL
            |--------------------------------------------------------------------------
            */

            $totalAmount =
                $orderSubtotal
                + $order->shipping_fee
                - $order->discount_amount;

            DB::table('orders')
                ->where(
                    'order_id',
                    $order->order_id
                )
                ->update([

                    'subtotal' =>
                        $orderSubtotal,

                    'total_amount' =>
                        $totalAmount,
                ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE REVENUE REPORT
            |--------------------------------------------------------------------------
            */

            if (
                $order->payment_status === 'paid'
            ) {

                $reportDate =
                    Carbon::parse(
                        $order->created_at
                    )->toDateString();

                $report =
                    DB::table(
                        'revenue_reports'
                    )
                        ->where(
                            'report_date',
                            $reportDate
                        )
                        ->first();

                $itemsSold =
                    DB::table('order_items')
                        ->where(
                            'order_id',
                            $order->order_id
                        )
                        ->sum(
                            'quantity'
                        );

                if ($report) {

                    $newRevenue =
                        $report->total_revenue
                        + $totalAmount;

                    $newOrders =
                        $report->total_orders
                        + 1;

                    $newItems =
                        $report->total_items_sold
                        + $itemsSold;

                    DB::table(
                        'revenue_reports'
                    )
                        ->where(
                            'report_id',
                            $report->report_id
                        )
                        ->update([

                            'total_revenue' =>
                                $newRevenue,

                            'total_orders' =>
                                $newOrders,

                            'total_items_sold' =>
                                $newItems,

                            'avg_order_value' =>
                                round(
                                    $newRevenue /
                                    $newOrders
                                ),
                        ]);

                } else {

                    DB::table(
                        'revenue_reports'
                    )
                        ->insert([

                            'report_date' =>
                                $reportDate,

                            'total_revenue' =>
                                $totalAmount,

                            'total_orders' =>
                                1,

                            'total_items_sold' =>
                                $itemsSold,

                            'avg_order_value' =>
                                $totalAmount,
                        ]);
                }
            }
        }
    }
}