<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiProductSeeder extends Seeder
{
    public function run(): void
    {
        // Gọi API từ DummyJSON lấy 30 sản phẩm công nghệ (Smartphones, Laptops)
        // Bạn có thể đổi limit=100 để lấy nhiều hơn
        $response = Http::withoutVerifying()->get('https://dummyjson.com/products/search?q=phone&limit=30');

        if ($response->successful()) {
            $products = $response->json()['products'];

            foreach ($products as $item) {
                // Đổi giá từ USD sang VND (Giả sử 1 USD = 25,000 VND)
                $priceVND = $item['price'] * 25000;

                DB::table('products')->insert([
                    'category_id' => rand(1, 5), // Random ID danh mục từ 1-5
                    'brand_id'    => rand(1, 10), // Random ID thương hiệu từ 1-10
                    'name'        => $item['title'],
                    'slug'        => Str::slug($item['title']) . '-' . rand(1000, 9999),
                    'description' => $item['description'],
                    
                    // Tạo thông số kỹ thuật ảo từ dữ liệu API
                    'specs'       => json_encode([
                        'Rating' => $item['rating'] . ' sao',
                        'Stock'  => $item['stock'] . ' sản phẩm',
                        'Brand'  => $item['brand'] ?? 'OEM'
                    ], JSON_UNESCAPED_UNICODE),

                    'base_price'  => $priceVND,
                    
                    // Random các trạng thái (1 là Có, 0 là Không)
                    'is_active'   => 1,
                    'is_new'      => rand(0, 1),
                    'is_hot'      => rand(0, 1),
                    'is_trending' => rand(0, 1),
                    
                    'view_count'  => rand(100, 5000),
                    'created_at'  => now()->subDays(rand(1, 30)), // Random ngày tạo trong 30 ngày qua
                ]);
            }

            $this->command->info('Đã kéo thành công dữ liệu từ API vào Database!');
        } else {
            $this->command->error('Lỗi kết nối API!');
        }
    }
}