<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại và dọn sạch bảng để tránh xung đột ID
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_images')->truncate();

        $data = [
            // [product_id, image_url]
            [1, 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%2013%20Pro/1.png'], // iPhone 17 Pro Max
            [2, 'https://cdn.dummyjson.com/products/images/smartphones/Samsung%20Galaxy%20S10/1.png'], // Samsung Galaxy S26 Ultra
            [3, 'https://cdn.dummyjson.com/products/images/smartphones/Oppo%20F19%20Pro%20Plus/1.png'], // Xiaomi 16 Ultra Leica
            [4, 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%20X/1.png'],           // Google Pixel 10 Pro XL
            [5, 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%2013%20Pro/2.png'], // iPhone 16 Pro Silver
            [6, 'https://cdn.dummyjson.com/products/images/smartphones/Samsung%20Galaxy%20S8/1.png'],  // Samsung Galaxy Z Fold 7
            [7, 'https://cdn.dummyjson.com/products/images/smartphones/Realme%20XT/1.png'],           // ASUS ROG Phone 10 Pro
            [8, 'https://cdn.dummyjson.com/products/images/laptops/MacBook%20Pro/1.png'],            // MacBook Pro M5 Max 14"
            [9, 'https://cdn.dummyjson.com/products/images/laptops/Microsoft%20Surface%20Laptop%204/1.png'], // Dell XPS 14 OLED 2026
            [10, 'https://cdn.dummyjson.com/products/images/laptops/HP%20Pavilion%2015-dk1056wm/1.png'],    // ASUS ROG Zephyrus G16
            [11, 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20AirPods%20Max%20Silver/1.png'], // AirPods Pro Gen 3
            [12, 'https://cdn.dummyjson.com/products/images/mobile-accessories/Beats%20Flex%20Wireless%20Earphones/1.png'], // Sony WH-1000XM6
        ];

        foreach ($data as $item) {
            DB::table('product_images')->insert([
                'product_id' => $item[0],
                'image_url'  => $item[1],
                'sort_order' => 1,
                'is_primary' => 1,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}