<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataSampleSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('product_variants')->truncate();
        DB::table('product_images')->truncate();
        DB::table('review_images')->truncate();
        DB::table('product_reviews')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('brands')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. THÊM BRAND
        DB::table('brands')->insert([
            ['brand_id' => 1, 'name' => 'Apple', 'slug' => 'apple', 'is_active' => 1],
            ['brand_id' => 2, 'name' => 'Samsung', 'slug' => 'samsung', 'is_active' => 1],
            ['brand_id' => 3, 'name' => 'Sony', 'slug' => 'sony', 'is_active' => 1],
            ['brand_id' => 4, 'name' => 'Dell', 'slug' => 'dell', 'is_active' => 1],
            ['brand_id' => 5, 'name' => 'ASUS', 'slug' => 'asus', 'is_active' => 1],
            ['brand_id' => 6, 'name' => 'Logitech', 'slug' => 'logitech', 'is_active' => 1],
        ]);

        // 2. THÊM CATEGORY
        DB::table('categories')->insert([
            ['category_id' => 1, 'name' => 'Điện thoại', 'slug' => 'dien-thoai', 'version' => 1, 'is_active' => 1],
            ['category_id' => 2, 'name' => 'Laptop', 'slug' => 'laptop', 'version' => 1, 'is_active' => 1],
            ['category_id' => 3, 'name' => 'Âm thanh', 'slug' => 'am-thanh', 'version' => 1, 'is_active' => 1],
            ['category_id' => 4, 'name' => 'Phụ kiện', 'slug' => 'phu-kien', 'version' => 1, 'is_active' => 1],
        ]);

        // 3. THÊM PRODUCT (Kèm theo link ảnh riêng biệt)
        $products = [

            // =========================
            // ĐIỆN THOẠI
            // =========================
            [
                'product_id' => 1,
                'category_id' => 1,
                'brand_id' => 1,
                'name' => 'iPhone 17 Pro Max',
                'slug' => 'iphone-17-pro-max',
                'base_price' => 35990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9'
            ],
            [
                'product_id' => 2,
                'category_id' => 1,
                'brand_id' => 2,
                'name' => 'Samsung Galaxy S26 Ultra',
                'slug' => 'samsung-galaxy-s26-ultra',
                'base_price' => 32990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97'
            ],
            [
                'product_id' => 3,
                'category_id' => 1,
                'brand_id' => 1,
                'name' => 'iPhone 16',
                'slug' => 'iphone-16',
                'base_price' => 22990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1495435229349-e86db7bfa013'
            ],
            [
                'product_id' => 4,
                'category_id' => 1,
                'brand_id' => 2,
                'name' => 'Samsung Galaxy A76',
                'slug' => 'samsung-galaxy-a76',
                'base_price' => 12990000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5'
            ],
            [
                'product_id' => 5,
                'category_id' => 1,
                'brand_id' => 2,
                'name' => 'Samsung Galaxy Z Fold 8',
                'slug' => 'samsung-z-fold-8',
                'base_price' => 41990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1580910051074-3eb694886505'
            ],

            // =========================
            // LAPTOP
            // =========================
            [
                'product_id' => 6,
                'category_id' => 2,
                'brand_id' => 4,
                'name' => 'Dell XPS 14 OLED',
                'slug' => 'dell-xps-14-oled',
                'base_price' => 48900000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853'
            ],
            [
                'product_id' => 7,
                'category_id' => 2,
                'brand_id' => 1,
                'name' => 'MacBook Air M4',
                'slug' => 'macbook-air-m4',
                'base_price' => 32990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1517336714739-489689fd1ca8'
            ],
            [
                'product_id' => 8,
                'category_id' => 2,
                'brand_id' => 5,
                'name' => 'ASUS ROG Strix G16',
                'slug' => 'asus-rog-strix-g16',
                'base_price' => 38990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6'
            ],
            [
                'product_id' => 9,
                'category_id' => 2,
                'brand_id' => 4,
                'name' => 'Dell Inspiron 15',
                'slug' => 'dell-inspiron-15',
                'base_price' => 18990000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4'
            ],
            [
                'product_id' => 10,
                'category_id' => 2,
                'brand_id' => 5,
                'name' => 'ASUS Vivobook 15',
                'slug' => 'asus-vivobook-15',
                'base_price' => 15990000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2'
            ],

            // =========================
            // ÂM THANH
            // =========================
            [
                'product_id' => 11,
                'category_id' => 3,
                'brand_id' => 3,
                'name' => 'Sony WH-1000XM6',
                'slug' => 'sony-wh1000xm6',
                'base_price' => 8990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e'
            ],
            [
                'product_id' => 12,
                'category_id' => 3,
                'brand_id' => 1,
                'name' => 'AirPods Pro 3',
                'slug' => 'airpods-pro-3',
                'base_price' => 6990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1588423771073-b8903fbb85b5'
            ],
            [
                'product_id' => 13,
                'category_id' => 3,
                'brand_id' => 3,
                'name' => 'Sony WF-1000XM5',
                'slug' => 'sony-wf-1000xm5',
                'base_price' => 5990000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b'
            ],
            [
                'product_id' => 14,
                'category_id' => 3,
                'brand_id' => 2,
                'name' => 'Samsung Galaxy Buds 3 Pro',
                'slug' => 'galaxy-buds-3-pro',
                'base_price' => 4990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb'
            ],

            // =========================
            // PHỤ KIỆN
            // =========================
            [
                'product_id' => 15,
                'category_id' => 4,
                'brand_id' => 6,
                'name' => 'Logitech MX Master 4',
                'slug' => 'logitech-mx-master-4',
                'base_price' => 3290000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46'
            ],
            [
                'product_id' => 16,
                'category_id' => 4,
                'brand_id' => 6,
                'name' => 'Logitech G Pro X',
                'slug' => 'logitech-g-pro-x',
                'base_price' => 2490000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3'
            ],
            [
                'product_id' => 17,
                'category_id' => 4,
                'brand_id' => 6,
                'name' => 'Logitech K380',
                'slug' => 'logitech-k380',
                'base_price' => 890000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae'
            ],
            [
                'product_id' => 18,
                'category_id' => 4,
                'brand_id' => 1,
                'name' => 'Apple Magic Mouse',
                'slug' => 'apple-magic-mouse',
                'base_price' => 2290000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1563297007-0686b7003af7'
            ],

            // =========================
            // TABLET
            // =========================
            [
                'product_id' => 19,
                'category_id' => 1,
                'brand_id' => 1,
                'name' => 'iPad Pro M5',
                'slug' => 'ipad-pro-m5',
                'base_price' => 28990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0'
            ],
            [
                'product_id' => 20,
                'category_id' => 1,
                'brand_id' => 2,
                'name' => 'Galaxy Tab S10',
                'slug' => 'galaxy-tab-s10',
                'base_price' => 21990000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1585790050230-5dd28404ccb9'
            ],

            // =========================
            // THÊM RANDOM CHO ĐỦ 30
            // =========================
            [
                'product_id' => 21,
                'category_id' => 2,
                'brand_id' => 5,
                'name' => 'ASUS TUF Gaming F15',
                'slug' => 'asus-tuf-f15',
                'base_price' => 24990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1611078489935-0cb964de46d6'
            ],
            [
                'product_id' => 22,
                'category_id' => 2,
                'brand_id' => 4,
                'name' => 'Dell Alienware M18',
                'slug' => 'alienware-m18',
                'base_price' => 65990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1598550476439-6847785fcea6'
            ],
            [
                'product_id' => 23,
                'category_id' => 4,
                'brand_id' => 6,
                'name' => 'Logitech G502 X',
                'slug' => 'logitech-g502-x',
                'base_price' => 1590000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1613141412501-9012977f1969'
            ],
            [
                'product_id' => 24,
                'category_id' => 3,
                'brand_id' => 3,
                'name' => 'Sony SRS-XB43',
                'slug' => 'sony-srs-xb43',
                'base_price' => 4290000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d'
            ],
            [
                'product_id' => 25,
                'category_id' => 1,
                'brand_id' => 1,
                'name' => 'iPhone SE 4',
                'slug' => 'iphone-se-4',
                'base_price' => 14990000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1512499617640-c2f999098c01'
            ],
            [
                'product_id' => 26,
                'category_id' => 1,
                'brand_id' => 2,
                'name' => 'Galaxy S25 FE',
                'slug' => 'galaxy-s25-fe',
                'base_price' => 16990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1567581935884-3349723552ca'
            ],
            [
                'product_id' => 27,
                'category_id' => 4,
                'brand_id' => 6,
                'name' => 'Logitech StreamCam',
                'slug' => 'logitech-streamcam',
                'base_price' => 2790000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1587614382346-4ec70e388b28'
            ],
            [
                'product_id' => 28,
                'category_id' => 2,
                'brand_id' => 1,
                'name' => 'MacBook Pro M5',
                'slug' => 'macbook-pro-m5',
                'base_price' => 58990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4'
            ],
            [
                'product_id' => 29,
                'category_id' => 3,
                'brand_id' => 3,
                'name' => 'Sony HT-A7000',
                'slug' => 'sony-ht-a7000',
                'base_price' => 24990000,
                'is_trending' => 0,
                'thumb' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d'
            ],
            [
                'product_id' => 30,
                'category_id' => 4,
                'brand_id' => 1,
                'name' => 'Apple Watch Series 11',
                'slug' => 'apple-watch-series-11',
                'base_price' => 12990000,
                'is_trending' => 1,
                'thumb' => 'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9'
            ],
        ];

        foreach ($products as $p) {
            // Tách thumb ra để insert vào bảng images sau
            $thumbUrl = $p['thumb'];
            unset($p['thumb']);

            // Insert sản phẩm
            DB::table('products')->insert(array_merge($p, [
                'description' => 'Mô tả chi tiết cho ' . $p['name'] . '. Sản phẩm công nghệ hàng đầu.',
                'is_active' => 1,
                'view_count' => rand(500, 2000),
                'created_at' => now()
            ]));

            // Insert ảnh tương ứng cho sản phẩm đó
            DB::table('product_images')->insert([
                'product_id' => $p['product_id'],
                'image_url' => $thumbUrl,
                'is_primary' => 1,
                'sort_order' => 1
            ]);
        }

        // 5. THÊM PRODUCT VARIANTS (Giữ nguyên)
        DB::table('product_variants')->insert([
            ['product_id' => 1, 'sku' => 'IP17-256-GOLD', 'price' => 35990000, 'stock_quantity' => 50, 'attribute_values' => json_encode(['Color' => 'Gold', 'Storage' => '256GB']), 'is_active' => 1],
            ['product_id' => 3, 'sku' => 'DELL-XPS-16GB', 'price' => 48900000, 'stock_quantity' => 15, 'attribute_values' => json_encode(['RAM' => '16GB', 'SSD' => '512GB']), 'is_active' => 1],
        ]);

        // 6. THÊM REVIEWS (Giữ nguyên)
        for ($j = 1; $j <= 5; $j++) {
            DB::table('product_reviews')->insert([
                'product_id' => rand(1, 3),
                'user_id' => 3,
                'rating' => rand(4, 5),
                'comment' => 'Sản phẩm tuyệt vời, dịch vụ tốt!',
                'status' => 'approved',
                'created_at' => now()
            ]);
        }
    }
}
