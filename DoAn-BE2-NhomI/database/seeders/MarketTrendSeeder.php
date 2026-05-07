<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketTrendSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::table('product_images')->truncate();

        // Danh sách ảnh mẫu chất lượng cao cho từng thương hiệu
        $imgMap = [
            'Apple' => 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%2013%20Pro/1.png',
            'Samsung' => 'https://cdn.dummyjson.com/products/images/smartphones/Samsung%20Galaxy%20S10/1.png',
            'Xiaomi' => 'https://cdn.dummyjson.com/products/images/smartphones/Oppo%20F19%20Pro%20Plus/1.png',
            'Google' => 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%20X/1.png',
            'Laptop' => 'https://cdn.dummyjson.com/products/images/laptops/MacBook%20Pro/1.png',
            'Accessory' => 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20AirPods%20Max%20Silver/1.png'
        ];

        $products = [
            // --- SMARTPHONES (Sử dụng ảnh theo Brand) ---
            ['n' => 'iPhone 17 Pro Max Titanium', 'c' => 1, 'b' => 1, 'p' => 35990000, 'h' => 1, 'img' => $imgMap['Apple']],
            ['n' => 'Samsung Galaxy S26 Ultra AI', 'c' => 1, 'b' => 2, 'p' => 32990000, 'h' => 1, 'img' => $imgMap['Samsung']],
            ['n' => 'Xiaomi 16 Ultra Leica', 'c' => 1, 'b' => 3, 'p' => 26500000, 'h' => 1, 'img' => $imgMap['Xiaomi']],
            ['n' => 'Google Pixel 10 Pro XL', 'c' => 1, 'b' => 4, 'p' => 27900000, 'h' => 0, 'img' => $imgMap['Google']],
            ['n' => 'iPhone 16 Pro Silver', 'c' => 1, 'b' => 1, 'p' => 28990000, 'h' => 0, 'img' => $imgMap['Apple']],
            ['n' => 'Samsung Galaxy Z Fold 7', 'c' => 1, 'b' => 2, 'p' => 41990000, 'h' => 1, 'img' => $imgMap['Samsung']],
            ['n' => 'ASUS ROG Phone 10 Pro', 'c' => 1, 'b' => 5, 'p' => 29990000, 'h' => 1, 'img' => 'https://cdn.dummyjson.com/products/images/smartphones/Realme%20XT/1.png'],
            
            // --- LAPTOPS (Sử dụng ảnh Laptop chuyên biệt) ---
            ['n' => 'MacBook Pro M5 Max 14"', 'c' => 2, 'b' => 1, 'p' => 54990000, 'h' => 1, 'img' => $imgMap['Laptop']],
            ['n' => 'Dell XPS 14 OLED 2026', 'c' => 2, 'b' => 6, 'p' => 48900000, 'h' => 1, 'img' => 'https://cdn.dummyjson.com/products/images/laptops/Microsoft%20Surface%20Laptop%204/1.png'],
            ['n' => 'ASUS ROG Zephyrus G16', 'c' => 2, 'b' => 5, 'p' => 62000000, 'h' => 1, 'img' => 'https://cdn.dummyjson.com/products/images/laptops/HP%20Pavilion%2015-dk1056wm/1.png'],
            
            // --- PHỤ KIỆN ---
            ['n' => 'AirPods Pro Gen 3', 'c' => 3, 'b' => 1, 'p' => 6500000, 'h' => 1, 'img' => 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20AirPods%20Max%20Silver/1.png'],
            ['n' => 'Sony WH-1000XM6', 'c' => 3, 'b' => 8, 'p' => 9990000, 'h' => 1, 'img' => 'https://cdn.dummyjson.com/products/images/mobile-accessories/Beats%20Flex%20Wireless%20Earphones/1.png'],
            // ... Bạn có thể copy-paste thêm các sản phẩm khác vào đây dựa trên danh sách 40 sản phẩm trước đó
        ];

        foreach ($products as $item) {
            $pid = DB::table('products')->insertGetId([
                'category_id' => $item['c'],
                'brand_id' => $item['b'],
                'name' => $item['n'],
                'slug' => Str::slug($item['n']) . '-' . Str::random(4),
                'description' => 'Siêu phẩm ' . $item['n'] . ' chính hãng tại B-TRIS. Trải nghiệm công nghệ đỉnh cao năm 2026.',
                'base_price' => $item['p'],
                'is_active' => 1,
                'is_hot' => $item['h'],
                'created_at' => now(),
            ]);

            DB::table('product_images')->insert([
                'product_id' => $pid,
                'image_url' => $item['img'],
                'is_primary' => 1,
                'sort_order' => 1
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}