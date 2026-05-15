<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MissingBusinessTablesSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | shipping_fees
        |--------------------------------------------------------------------------
        */
        DB::table('shipping_fees')->updateOrInsert(
            ['province' => 'Hà Nội'],
            ['fee' => 30000, 'estimated_days' => 2]
        );

        DB::table('shipping_fees')->updateOrInsert(
            ['province' => 'Hồ Chí Minh'],
            ['fee' => 35000, 'estimated_days' => 3]
        );

        DB::table('shipping_fees')->updateOrInsert(
            ['province' => 'Đà Nẵng'],
            ['fee' => 40000, 'estimated_days' => 4]
        );

        /*
        |--------------------------------------------------------------------------
        | vouchers
        |--------------------------------------------------------------------------
        */
        DB::table('vouchers')->updateOrInsert(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percent',
                'value' => 10,
                'min_order_value' => 1000000,
                'max_discount' => 500000,
                'usage_limit' => 100,
                'used_count' => 0,
                'start_at' => null,
                'end_at' => null,
                'is_active' => 1,
            ]
        );

        DB::table('vouchers')->updateOrInsert(
            ['code' => 'FREESHIP50K'],
            [
                'type' => 'fixed',
                'value' => 50000,
                'min_order_value' => 500000,
                'max_discount' => null,
                'usage_limit' => 50,
                'used_count' => 0,
                'start_at' => null,
                'end_at' => null,
                'is_active' => 1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | shipping_addresses
        |--------------------------------------------------------------------------
        */
        DB::table('shipping_addresses')->updateOrInsert(
            ['address_id' => 1],
            [
                'user_id' => 3,
                'full_name' => 'Nguyễn Văn A',
                'phone' => '0923456789',
                'province' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Dịch Vọng',
                'street_address' => 'Số 10, Ngõ 1',
                'is_default' => 1,
            ]
        );

        DB::table('shipping_addresses')->updateOrInsert(
            ['address_id' => 2],
            [
                'user_id' => 4,
                'full_name' => 'Trần Thị B',
                'phone' => '0934567890',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 1',
                'ward' => 'Bến Nghé',
                'street_address' => 'Tòa nhà ABC, Lê Lợi',
                'is_default' => 1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | carts
        |--------------------------------------------------------------------------
        */
        DB::table('carts')->updateOrInsert(
            ['cart_id' => 1],
            [
                'user_id' => 3,
                'session_id' => null,
                'updated_at' => now(),
            ]
        );

        DB::table('carts')->updateOrInsert(
            ['cart_id' => 2],
            [
                'user_id' => null,
                'session_id' => 'guest-session-abc-12345',
                'updated_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | cart_items
        |--------------------------------------------------------------------------
        */
        DB::table('cart_items')->updateOrInsert(
            ['item_id' => 1],
            [
                'cart_id' => 1,
                'variant_id' => 1,
                'quantity' => 1,
                'price' => 28990000,
            ]
        );

        DB::table('cart_items')->updateOrInsert(
            ['item_id' => 2],
            [
                'cart_id' => 1,
                'variant_id' => 3,
                'quantity' => 2,
                'price' => 30990000,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | orders
        |--------------------------------------------------------------------------
        */
        DB::table('orders')->updateOrInsert(
            ['order_code' => 'ORD-20240501-001'],
            [
                'user_id' => 3,
                'shipping_address_id' => 1,
                'voucher_id' => 1,
                'subtotal' => 28990000,
                'shipping_fee' => 30000,
                'discount_amount' => 500000,
                'total_amount' => 28520000,
                'payment_method' => 'vnpay',
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'cancel_reason' => null,
                'paid_at' => null,
                'created_at' => now(),
            ]
        );

        DB::table('orders')->updateOrInsert(
            ['order_code' => 'ORD-20240502-002'],
            [
                'user_id' => 4,
                'shipping_address_id' => 2,
                'voucher_id' => null,
                'subtotal' => 30990000,
                'shipping_fee' => 35000,
                'discount_amount' => 0,
                'total_amount' => 31025000,
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'order_status' => 'confirmed',
                'cancel_reason' => null,
                'paid_at' => null,
                'created_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | order_items
        |--------------------------------------------------------------------------
        */
        DB::table('order_items')->updateOrInsert(
            ['order_item_id' => 1],
            [
                'order_id' => 1,
                'variant_id' => 1,
                'product_name' => 'iPhone 15 Pro Max',
                'variant_info' => '256GB, Titan Tự Nhiên',
                'unit_price' => 28990000,
                'quantity' => 1,
                'subtotal' => 28990000,
            ]
        );

        DB::table('order_items')->updateOrInsert(
            ['order_item_id' => 2],
            [
                'order_id' => 2,
                'variant_id' => 3,
                'product_name' => 'Samsung Galaxy S24 Ultra',
                'variant_info' => '256GB, Đen',
                'unit_price' => 30990000,
                'quantity' => 1,
                'subtotal' => 30990000,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | payments
        |--------------------------------------------------------------------------
        */
        DB::table('payments')->updateOrInsert(
            ['transaction_id' => 'VNP123456789'],
            [
                'order_id' => 1,
                'gateway' => 'vnpay',
                'amount' => 28520000,
                'status' => 'success',
                'gateway_response' => null,
                'paid_at' => null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | inventory_logs
        |--------------------------------------------------------------------------
        */
        DB::table('inventory_logs')->updateOrInsert(
            ['log_id' => 1],
            [
                'variant_id' => 1,
                'order_id' => null,
                'action' => 'import',
                'quantity_change' => 51,
                'quantity_after' => 51,
                'note' => 'Nhập hàng đợt 1',
                'created_by' => 1,
                'created_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | price_histories
        |--------------------------------------------------------------------------
        */
        DB::table('price_histories')->updateOrInsert(
            ['history_id' => 1],
            [
                'variant_id' => 1,
                'old_price' => 29990000,
                'new_price' => 28990000,
                'changed_by' => 1,
                'changed_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | revenue_reports
        |--------------------------------------------------------------------------
        */
        DB::table('revenue_reports')->updateOrInsert(
            ['report_date' => '2026-04-23'],
            [
                'total_revenue' => 28520000,
                'total_orders' => 1,
                'total_items_sold' => 1,
                'avg_order_value' => 28520000,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | backup_logs
        |--------------------------------------------------------------------------
        */
        DB::table('backup_logs')->updateOrInsert(
            ['backup_id' => 1],
            [
                'file_name' => 'backup_20240501.sql',
                'file_path' => '/storage/backups/backup_20240501.sql',
                'file_size' => 15485760,
                'status' => 'success',
                'created_by' => 1,
                'created_at' => now(),
            ]
        );
    }
}