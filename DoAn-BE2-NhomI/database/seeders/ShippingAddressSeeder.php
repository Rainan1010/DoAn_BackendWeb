<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingAddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [];

        $locations = [
            [
                'province' => 'Thành phố Hồ Chí Minh',
                'district' => 'Thành phố Thủ Đức',
                'ward' => 'Phường Linh Trung',
            ],
            [
                'province' => 'Thành phố Hồ Chí Minh',
                'district' => 'Thành phố Thủ Đức',
                'ward' => 'Phường Linh Chiểu',
            ],
            [
                'province' => 'Thành phố Hồ Chí Minh',
                'district' => 'Thành phố Thủ Đức',
                'ward' => 'Phường Hiệp Bình Chánh',
            ],
            [
                'province' => 'Thành phố Hà Nội',
                'district' => 'Quận Cầu Giấy',
                'ward' => 'Phường Dịch Vọng',
            ],
            [
                'province' => 'Thành phố Hà Nội',
                'district' => 'Quận Ba Đình',
                'ward' => 'Phường Kim Mã',
            ],
            [
                'province' => 'Thành phố Đà Nẵng',
                'district' => 'Quận Hải Châu',
                'ward' => 'Phường Thạch Thang',
            ],
            [
                'province' => 'Thành phố Cần Thơ',
                'district' => 'Quận Ninh Kiều',
                'ward' => 'Phường An Khánh',
            ],
            [
                'province' => 'Tỉnh Bình Dương',
                'district' => 'Thành phố Thuận An',
                'ward' => 'Phường Lái Thiêu',
            ],
            [
                'province' => 'Tỉnh Đồng Nai',
                'district' => 'Thành phố Biên Hòa',
                'ward' => 'Phường Tân Tiến',
            ],
            [
                'province' => 'Tỉnh Khánh Hòa',
                'district' => 'Thành phố Nha Trang',
                'ward' => 'Phường Vĩnh Hải',
            ],
        ];

        $fullNames = [
            'Nguyen Van An',
            'Tran Minh Khang',
            'Le Hoang Nam',
            'Pham Gia Bao',
            'Vo Thanh Tung',
            'Dang Quoc Huy',
            'Bui Tuan Kiet',
            'Do Minh Quan',
            'Phan Duc Anh',
            'Hoang Gia Huy',
        ];

        $phonePrefixes = ['09', '08', '07', '03', '02'];

        // User ID từ 1 -> 23
        for ($userId = 1; $userId <= 23; $userId++) {

            $fullName = $fullNames[($userId - 1) % count($fullNames)];

            for ($i = 0; $i < 10; $i++) {

                $location = $locations[$i];

                $addresses[] = [
                    'user_id' => $userId,

                    'full_name' => $fullName,

                    'phone' => $phonePrefixes[array_rand($phonePrefixes)]
                        . rand(10000000, 99999999),

                    'province' => $location['province'],

                    'district' => $location['district'],

                    'ward' => $location['ward'],

                    'street_address' =>
                        'So ' .
                        (($userId * 100) + $i + 1) .
                        ' Duong Nguyen Van ' .
                        chr(65 + $i),

                    // Địa chỉ đầu tiên là mặc định
                    'is_default' => $i === 0 ? 1 : 0,
                ];
            }
        }

        DB::table('shipping_addresses')->insert($addresses);
    }
}