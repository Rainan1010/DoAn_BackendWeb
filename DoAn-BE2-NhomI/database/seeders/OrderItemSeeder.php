<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [

            // ORDER 5
            [
                'order_id' => 5,
                'variant_id' => 1,
                'quantity' => 1,
            ],

            // ORDER 6
            [
                'order_id' => 6,
                'variant_id' => 4,
                'quantity' => 1,
            ],

            // ORDER 7
            [
                'order_id' => 7,
                'variant_id' => 3,
                'quantity' => 1,
            ],

            // ORDER 8
            [
                'order_id' => 8,
                'variant_id' => 6,
                'quantity' => 1,
            ],

            // ORDER 9 ITEM 1
            [
                'order_id' => 9,
                'variant_id' => 5,
                'quantity' => 1,
            ],

            // ORDER 9 ITEM 2
            [
                'order_id' => 9,
                'variant_id' => 8,
                'quantity' => 1,
            ],

            // ORDER 10
            [
                'order_id' => 10,
                'variant_id' => 9,
                'quantity' => 2,
            ],
        ];

        foreach ($items as $item) {

            $variant = DB::table('product_variants')
                ->join('products', 'products.product_id', '=', 'product_variants.product_id')
                ->where('product_variants.variant_id', $item['variant_id'])
                ->select(
                    'products.name',
                    'product_variants.sale_price',
                    'product_variants.price',
                    'product_variants.attribute_values'
                )
                ->first();

            $price = $variant->sale_price ?? $variant->price;

            DB::table('order_items')->insert([

                'order_id' => $item['order_id'],

                'variant_id' => $item['variant_id'],

                'product_name' => $variant->name,

                'variant_info' => $variant->attribute_values,

                'unit_price' => $price,

                'quantity' => $item['quantity'],

                'subtotal' => $price * $item['quantity'],
            ]);
        }
    }
}