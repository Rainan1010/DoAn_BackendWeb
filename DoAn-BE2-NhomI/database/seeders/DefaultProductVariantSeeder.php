<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $products = DB::table('products')->get();

        foreach ($products as $product) {
            $exists = DB::table('product_variants')
                ->where('product_id', $product->product_id)
                ->exists();

            if (!$exists) {
                DB::table('product_variants')->insert([
                    'product_id' => $product->product_id,
                    'sku' => 'SP-' . $product->product_id . '-DEFAULT',
                    'price' => $product->base_price ?? 0,
                    'sale_price' => null,
                    'stock_quantity' => 100,
                    'attribute_values' => json_encode([
                        'default' => 'Mặc định',
                    ], JSON_UNESCAPED_UNICODE),
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}