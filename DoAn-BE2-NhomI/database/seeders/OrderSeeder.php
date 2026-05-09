<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('orders')->insert([

            // ORDER 1
            [
                'user_id' => 1,
                'shipping_address_id' => 3,
                'voucher_id' => null,
                'order_code' => 'BT-1001',
                'subtotal' => 34990000,
                'shipping_fee' => 30000,
                'discount_amount' => 0,
                'total_amount' => 35020000,
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'cancel_reason' => null,
                'paid_at' => null,
                'created_at' => now(),
            ],

            // ORDER 2
            [
                'user_id' => 1,
                'shipping_address_id' => 4,
                'voucher_id' => null,
                'order_code' => 'BT-1002',
                'subtotal' => 28500000,
                'shipping_fee' => 30000,
                'discount_amount' => 500000,
                'total_amount' => 28030000,
                'payment_method' => 'momo',
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
                'cancel_reason' => null,
                'paid_at' => now(),
                'created_at' => now(),
            ],

            // ORDER 3
            [
                'user_id' => 1,
                'shipping_address_id' => 3,
                'voucher_id' => null,
                'order_code' => 'BT-1003',
                'subtotal' => 21990000,
                'shipping_fee' => 30000,
                'discount_amount' => 1000000,
                'total_amount' => 21020000,
                'payment_method' => 'vnpay',
                'payment_status' => 'paid',
                'order_status' => 'shipped',
                'cancel_reason' => null,
                'paid_at' => now(),
                'created_at' => now(),
            ],

            // ORDER 4
            [
                'user_id' => 1,
                'shipping_address_id' => 3,
                'voucher_id' => null,
                'order_code' => 'BT-1004',
                'subtotal' => 15990000,
                'shipping_fee' => 30000,
                'discount_amount' => 0,
                'total_amount' => 16020000,
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'order_status' => 'processing',
                'cancel_reason' => null,
                'paid_at' => null,
                'created_at' => now(),
            ],

            // ORDER 5
            [
                'user_id' => 1,
                'shipping_address_id' =>4,
                'voucher_id' => null,
                'order_code' => 'BT-1005',
                'subtotal' => 45990000,
                'shipping_fee' => 30000,
                'discount_amount' => 2000000,
                'total_amount' => 44020000,
                'payment_method' => 'momo',
                'payment_status' => 'paid',
                'order_status' => 'delivered',
                'cancel_reason' => null,
                'paid_at' => now(),
                'created_at' => now(),
            ],

            // ORDER 6
            [
                'user_id' => 1,
                'shipping_address_id' => 4,
                'voucher_id' => null,
                'order_code' => 'BT-1006',
                'subtotal' => 12500000,
                'shipping_fee' => 30000,
                'discount_amount' => 0,
                'total_amount' => 12530000,
                'payment_method' => 'vnpay',
                'payment_status' => 'refunded',
                'order_status' => 'cancelled',
                'cancel_reason' => 'Khách yêu cầu huỷ',
                'paid_at' => now(),
                'created_at' => now(),
            ],

        ]);
    }
}