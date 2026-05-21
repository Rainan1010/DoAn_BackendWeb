<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompareController extends Controller
{

    public function getCompareProduct($id)
    {
        // Truy vấn sản phẩm kèm ảnh chính
        $product = DB::table('products')
            ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
            ->where('products.product_id', $id)
            ->where('product_images.is_primary', 1)
            ->select('products.*', 'product_images.image_url')
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
        }

        // Logic giả lập thông số (Khi bạn có specs thật trong DB thì thay bằng $product->specs)
        $specs = [
            'chipset' => str_contains($product->name, 'iPhone') ? 'Apple A18 Pro' : 'Snapdragon 8 Gen 4',
            'camera' => str_contains($product->name, 'Ultra') ? '200MP + 50MP + 12MP' : '48MP + 12MP + 12MP',
            'battery' => '5.000 mAh, 65W',
        ];

        return response()->json([
            'name'  => $product->name,
            'image' => asset(str_replace('public/', '', $product->image_url)),
            'price' => number_format($product->base_price, 0, ',', '.') . '₫',
            'specs' => $specs
        ]);
    }
}
