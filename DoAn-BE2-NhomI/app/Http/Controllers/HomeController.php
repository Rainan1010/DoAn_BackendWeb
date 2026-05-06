<?php

namespace App\Http\Controllers;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Bắt buộc phải có dòng này để gọi Database

class HomeController extends Controller
{
    public function index()
    {
        // Tự động lấy 8 sản phẩm MỚI NHẤT dựa trên thời gian tạo (created_at)
        // Kết hợp với ảnh đại diện chính (is_primary)
        $newProducts = DB::table('products')
            ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
            ->where('product_images.is_primary', 1)
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.created_at', 'desc') // Sắp xếp mới nhất lên đầu
            ->limit(8) // Chỉ lấy 8 sản phẩm để hiển thị lưới 2x4 hoặc 4x2
            ->get();

        return view('home.index', compact('newProducts'));
    }
    public function detail($id)
    {
        // 🔥 lấy sản phẩm
        $product = Product::findOrFail($id);

        // 🔥 lấy ảnh chính
        $image = DB::table('product_images')
            ->where('product_id', $id)
            ->where('is_primary', 1)
            ->first();

        $product->image_url = $image->image_url ?? null;

        // 🔥 lấy variants (RAM / ROM / Màu)
        $variants = DB::table('product_variants')
            ->where('product_id', $id)
            ->where('is_active', 1)
            ->get();

        // 🔥 sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->limit(4)
            ->get();

        // 🔥 gắn ảnh cho sản phẩm liên quan
        foreach ($relatedProducts as $item) {
            $img = DB::table('product_images')
                ->where('product_id', $item->product_id)
                ->where('is_primary', 1)
                ->first();

            $item->image_url = $img->image_url ?? null;
        }

        return view('products.product_detail', compact(
            'product',
            'variants',
            'relatedProducts'
        ));
    }
}
