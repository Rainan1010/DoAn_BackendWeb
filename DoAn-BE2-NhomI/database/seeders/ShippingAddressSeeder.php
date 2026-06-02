<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 
        DB::table('shipping_addresses')->insert([
            [
                'user_id' => 1,
                'full_name' => 'Nguyễn Xuân Thu Trang',
                'phone' => '0123445678',
                'province' => 'Thành phố Hồ Chí Minh',
                'district' => 'Thành phố Thủ Đức',
                'ward' => 'Phường Hiệp Phú',
                'street_address' => '145/24',
                'is_default' => 1,
            ],

            [
                'user_id' => 1,
                'full_name' => 'Nguyễn Xuân Thu Trang',
                'phone' => '0901234567',
                'province' => 'Thành phố Hồ Chí Minh',
                'district' => 'Thành phố Thủ Đức',
                'ward' => 'Phường Hiệp Bình Chánh',
                'street_address' => '145/24 Quốc lộ 13',
                'is_default' => 0,
            ],

            [
                'user_id' => 2,
                'full_name' => 'Trần Thị B',
                'phone' => '0934567890',
                'province' => 'Hồ Chí Minh',
                'district' => 'Quận 1',
                'ward' => 'Bến Nghé',
                'street_address' => 'Tòa nhà ABC, Lê Lợi',
                'is_default' => 1,
            ],
            [
                'user_id' => 2,
                'full_name' => 'Trần Thị B',
                'phone' => '0988888888',
                'province' => 'Đà Nẵng',
                'district' => 'Hải Châu',
                'ward' => 'Phường Thạch Thang',
                'street_address' => '12 Nguyễn Văn Linh',
                'is_default' => 0,
            ],
            [
                'user_id' => 3,
                'full_name' => 'Nguyễn Văn A',
                'phone' => '0923456789',
                'province' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Dịch Vọng',
                'street_address' => 'Số 10, Ngõ 1',
                'is_default' => 0,
            ],
        ]);

    }
}
