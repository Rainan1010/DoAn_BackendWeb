<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attribute_values')->delete();
        DB::table('attributes')->delete();

        DB::table('attributes')->insert([
            [
                'attribute_id' => 1,
                'name' => 'RAM',
                'unit' => 'GB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attribute_id' => 2,
                'name' => 'Bộ nhớ trong (ROM)',
                'unit' => 'GB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attribute_id' => 3,
                'name' => 'Màu sắc',
                'unit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('attribute_values')->insert([
            [
                'value_id' => 1,
                'attribute_id' => 1,
                'value' => '8',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value_id' => 2,
                'attribute_id' => 1,
                'value' => '16',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value_id' => 3,
                'attribute_id' => 2,
                'value' => '256',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value_id' => 4,
                'attribute_id' => 2,
                'value' => '512',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value_id' => 5,
                'attribute_id' => 2,
                'value' => '1024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value_id' => 6,
                'attribute_id' => 3,
                'value' => 'Đen (Black)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value_id' => 7,
                'attribute_id' => 3,
                'value' => 'Trắng (White)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'value_id' => 8,
                'attribute_id' => 3,
                'value' => 'Titan Tự Nhiên',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}