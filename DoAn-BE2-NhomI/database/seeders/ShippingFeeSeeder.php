<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingFeeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('shipping_fees')->truncate();

        $data = [

            // FREE SHIP
            ['province' => 'Hồ Chí Minh', 'fee' => 0, 'estimated_days' => 1],

            // MIỀN NAM
            ['province' => 'Bình Dương', 'fee' => 15000, 'estimated_days' => 1],
            ['province' => 'Đồng Nai', 'fee' => 15000, 'estimated_days' => 1],
            ['province' => 'Bà Rịa - Vũng Tàu', 'fee' => 20000, 'estimated_days' => 1],
            ['province' => 'Long An', 'fee' => 20000, 'estimated_days' => 1],
            ['province' => 'Tiền Giang', 'fee' => 25000, 'estimated_days' => 2],
            ['province' => 'Bến Tre', 'fee' => 25000, 'estimated_days' => 2],
            ['province' => 'Trà Vinh', 'fee' => 25000, 'estimated_days' => 2],
            ['province' => 'Vĩnh Long', 'fee' => 25000, 'estimated_days' => 2],
            ['province' => 'Cần Thơ', 'fee' => 25000, 'estimated_days' => 2],
            ['province' => 'Hậu Giang', 'fee' => 30000, 'estimated_days' => 2],
            ['province' => 'Sóc Trăng', 'fee' => 30000, 'estimated_days' => 2],
            ['province' => 'Bạc Liêu', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Cà Mau', 'fee' => 40000, 'estimated_days' => 3],
            ['province' => 'An Giang', 'fee' => 30000, 'estimated_days' => 2],
            ['province' => 'Kiên Giang', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Đồng Tháp', 'fee' => 30000, 'estimated_days' => 2],
            ['province' => 'Tây Ninh', 'fee' => 25000, 'estimated_days' => 2],
            ['province' => 'Bình Phước', 'fee' => 30000, 'estimated_days' => 2],

            // MIỀN TRUNG
            ['province' => 'Đà Nẵng', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Huế', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Quảng Nam', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Quảng Ngãi', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Bình Định', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Phú Yên', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Khánh Hòa', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Ninh Thuận', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Bình Thuận', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Quảng Trị', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Quảng Bình', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Hà Tĩnh', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Nghệ An', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Thanh Hóa', 'fee' => 40000, 'estimated_days' => 4],

            // TÂY NGUYÊN
            ['province' => 'Đắk Lắk', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Đắk Nông', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Gia Lai', 'fee' => 35000, 'estimated_days' => 3],
            ['province' => 'Kon Tum', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Lâm Đồng', 'fee' => 30000, 'estimated_days' => 2],

            // MIỀN BẮC
            ['province' => 'Hà Nội', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Hải Phòng', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Quảng Ninh', 'fee' => 45000, 'estimated_days' => 5],
            ['province' => 'Bắc Ninh', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Bắc Giang', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Hải Dương', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Hưng Yên', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Nam Định', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Thái Bình', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Ninh Bình', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Vĩnh Phúc', 'fee' => 40000, 'estimated_days' => 4],
            ['province' => 'Phú Thọ', 'fee' => 45000, 'estimated_days' => 5],
            ['province' => 'Thái Nguyên', 'fee' => 45000, 'estimated_days' => 5],
            ['province' => 'Lạng Sơn', 'fee' => 50000, 'estimated_days' => 5],
            ['province' => 'Cao Bằng', 'fee' => 50000, 'estimated_days' => 5],
            ['province' => 'Bắc Kạn', 'fee' => 50000, 'estimated_days' => 5],
            ['province' => 'Tuyên Quang', 'fee' => 50000, 'estimated_days' => 5],
            ['province' => 'Hà Giang', 'fee' => 55000, 'estimated_days' => 6],
            ['province' => 'Lào Cai', 'fee' => 55000, 'estimated_days' => 6],
            ['province' => 'Yên Bái', 'fee' => 50000, 'estimated_days' => 5],
            ['province' => 'Sơn La', 'fee' => 55000, 'estimated_days' => 6],
            ['province' => 'Điện Biên', 'fee' => 60000, 'estimated_days' => 6],
            ['province' => 'Lai Châu', 'fee' => 60000, 'estimated_days' => 6],
            ['province' => 'Hòa Bình', 'fee' => 45000, 'estimated_days' => 5],

        ];

        DB::table('shipping_fees')->insert($data);
    }
}