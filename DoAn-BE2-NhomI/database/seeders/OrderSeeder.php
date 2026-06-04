<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('revenue_reports')->truncate();
        DB::table('payments')->truncate();
        DB::table('orders')->truncate();

        $startDate = Carbon::create(2026, 5, 20);
        $endDate   = Carbon::create(2026, 6, 4);

        while ($startDate <= $endDate) {

            // MỖI NGÀY 10 ĐƠN
            for ($i = 1; $i <= 10; $i++) {

                $address = DB::table('shipping_addresses')
                    ->inRandomOrder()
                    ->first();

                if (!$address) {
                    continue;
                }

                $shippingFee = DB::table('shipping_fees')
                    ->where('province', $address->province)
                    ->value('fee') ?? 30000;

                $createdAt = $startDate->copy()->setTime(
                    rand(8, 22),
                    rand(0, 59),
                    rand(0, 59)
                );

                $paymentStatus = fake()->randomElement([
                    'paid',
                    'paid',
                    'paid',
                    'paid',
                    'paid',
                    'paid',
                    'paid',
                    'pending',
                    'pending',
                    'refunded',
                ]);

                $orderStatus = match ($paymentStatus) {

                    'paid' => fake()->randomElement([
                        'confirmed',
                        'processing',
                        'shipped',
                        'delivered',
                    ]),

                    'pending' => 'pending',

                    'refunded' => 'cancelled',
                };

                $subtotal = rand(
                    5000000,
                    48000000
                );

                $totalAmount = $subtotal + $shippingFee;

                // MOMO CHỈ DƯỚI 50 TRIỆU
                if ($totalAmount >= 50000000) {

                    $paymentMethod = fake()->randomElement([
                        'cod',
                        'vnpay',
                    ]);
                } else {

                    $paymentMethod = fake()->randomElement([
                        'cod',
                        'momo',
                        'vnpay',
                    ]);
                }

                $paidAt = $paymentStatus === 'paid'
                    ? $createdAt->copy()->addHours(rand(1, 5))
                    : null;

                $orderId = DB::table('orders')->insertGetId([

                    'user_id' => $address->user_id,

                    'shipping_address_id' => $address->address_id,

                    'voucher_id' => null,

                    'order_code' => 'ORD-' . strtoupper(Str::random(8)),

                    'subtotal' => $subtotal,

                    'shipping_fee' => $shippingFee,

                    'discount_amount' => 0,

                    'total_amount' => $totalAmount,

                    'payment_method' => $paymentMethod,

                    'payment_status' => $paymentStatus,

                    'order_status' => $orderStatus,

                    'cancel_reason' => $paymentStatus === 'refunded'
                        ? 'Khách yêu cầu huỷ đơn'
                        : null,

                    'paid_at' => $paidAt,

                    'created_at' => $createdAt,
                ]);

                /*
                |--------------------------------------------------------------------------
                | PAYMENT
                |--------------------------------------------------------------------------
                */

                DB::table('payments')->insert([

                    'order_id' => $orderId,

                    'gateway' => $paymentMethod,

                    'transaction_id' => match ($paymentMethod) {

                        'momo' => 'MOMO-' . strtoupper(uniqid()),

                        'vnpay' => 'VNPAY-' . strtoupper(uniqid()),

                        default => null,
                    },

                    'amount' => $totalAmount,

                    'status' => match ($paymentStatus) {

                        'paid' => 'success',

                        'pending' => 'pending',

                        'refunded' => 'refunded',

                        default => 'failed',
                    },

                    'gateway_response' => json_encode([
                        'message' => 'Seeder Payment'
                    ]),

                    'paid_at' => $paidAt,
                ]);

                /*
                |--------------------------------------------------------------------------
                | REVENUE REPORT
                |--------------------------------------------------------------------------
                */

                if ($paymentStatus === 'paid') {

                    $reportDate = $createdAt->toDateString();

                    $report = DB::table('revenue_reports')
                        ->where('report_date', $reportDate)
                        ->first();

                    if ($report) {

                        $newRevenue = $report->total_revenue + $totalAmount;
                        $newOrders  = $report->total_orders + 1;

                        DB::table('revenue_reports')
                            ->where('report_id', $report->report_id)
                            ->update([

                                'total_revenue' => $newRevenue,

                                'total_orders' => $newOrders,

                                'avg_order_value' => round(
                                    $newRevenue / $newOrders
                                ),
                            ]);
                    } else {

                        DB::table('revenue_reports')->insert([

                            'report_date' => $reportDate,

                            'total_revenue' => $totalAmount,

                            'total_orders' => 1,

                            'total_items_sold' => 0,

                            'avg_order_value' => $totalAmount,
                        ]);
                    }
                }
            }

            // CHỈ TĂNG NGÀY SAU KHI TẠO XONG 10 ĐƠN
            $startDate->addDay();
        }
    }
}