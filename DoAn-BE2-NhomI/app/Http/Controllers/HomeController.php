<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy sản phẩm cho BANNER TRÁI (Thỏa mãn: HOT + Mới tạo trong 7 ngày)
        // Chúng ta lấy 1 sản phẩm duy nhất
        $promoProduct = DB::table('products')
            ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
            ->where('product_images.is_primary', 1)
            ->where('products.is_hot', 1)
            ->where('products.created_at', '>=', now()->subDays(7))
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.created_at', 'desc')
            ->first();

        // 2. Lấy TẤT CẢ sản phẩm cho lưới bên phải và dùng PHÂN TRANG (Pagination)
        // Dùng paginate(12) để mỗi trang hiện 12 cái, tránh bị tràn màn hình và lỗi hàm links()
        $newProducts = DB::table('products')
            ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
            ->where('product_images.is_primary', 1)
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.created_at', 'desc')
            ->paginate(16); // Thay ->get() bằng ->paginate() để fix lỗi bạn gặp phải

        return view('home.index', compact('newProducts', 'promoProduct'));
    }

    public function detail($id)
    {
        // 🔥 Lấy sản phẩm
        $product = Product::findOrFail($id);

        // 🔥 Lấy ảnh chính
        $image = DB::table('product_images')
            ->where('product_id', $id)
            ->where('is_primary', 1)
            ->first();

        $product->image_url = $image->image_url ?? null;

        // 🔥 Lấy variants (RAM / ROM / Màu)
        $variants = DB::table('product_variants')
            ->where('product_id', $id)
            ->where('is_active', 1)
            ->get();

        // 🔥 Sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->limit(4)
            ->get();

        // 🔥 Gắn ảnh cho sản phẩm liên quan
        foreach ($relatedProducts as $item) {
            $img = DB::table('product_images')
                ->where('product_id', $item->product_id)
                ->where('is_primary', 1)
                ->first();

            $item->image_url = $img->image_url ?? null;
        }

        // 🔥 Lấy đánh giá
        $reviews = \App\Models\Review::with(['user', 'images'])
            ->where('product_id', $id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('products.product_detail', compact(
            'product',
            'variants',
            'relatedProducts',
            'reviews'
        ));
    }
}