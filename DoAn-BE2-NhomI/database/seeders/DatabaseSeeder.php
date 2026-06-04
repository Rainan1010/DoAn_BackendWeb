<?php

namespace Database\Seeders;

use App\Models\ShippingFee;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Thứ tự gọi rất quan trọng để tránh lỗi khóa ngoại (Foreign Key)
        $this->call([
            // 1. Chạy UserSeeder trước (nếu bạn đã có file này)
            UserSeeder::class,
            ShippingAddressSeeder::class,
            ShippingFeeSeeder::class,
            AttributeSeeder::class,
            
            // 2. Chạy DataSampleSeeder để đổ dữ liệu Sản phẩm, Review, Ảnh...
            DataSampleSeeder::class,
            DefaultProductVariantSeeder::class,

            //3.  đổ dữ liệu đơn hàng
            OrderSeeder::class,
            OrderItemSeeder::class,
        ]);
    }
}