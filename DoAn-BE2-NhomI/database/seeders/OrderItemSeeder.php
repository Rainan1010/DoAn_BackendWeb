<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('order_items')->truncate();

        $orders = DB::table('orders')->get();

        foreach ($orders as $order) {

            // mỗi đơn 1 -> 3 sản phẩm
            $itemCount = rand(1, 3);

            $usedVariants = [];

            for ($i = 0; $i < $itemCount; $i++) {

                $variant = DB::table('product_variants')
                    ->inRandomOrder()
                    ->first();

                if (!$variant) {
                    continue;
                }

                // tránh trùng variant trong cùng 1 đơn
                if (in_array($variant->variant_id, $usedVariants)) {
                    continue;
                }

                $usedVariants[] = $variant->variant_id;

                $product = DB::table('products')
                    ->where(
                        'product_id',
                        $variant->product_id
                    )
                    ->first();

                $price = $variant->sale_price
                    ? $variant->sale_price
                    : $variant->price;

                $quantity = rand(1, 3);

                DB::table('order_items')->insert([

                    'order_id' => $order->order_id,

                    'variant_id' => $variant->variant_id,

                    'product_name' => $product->name,

                    'variant_info' =>
                        $variant->attribute_values,

                    'unit_price' => $price,

                    'quantity' => $quantity,

                    'subtotal' =>
                        $price * $quantity,
                ]);
            }
        }
    }
}