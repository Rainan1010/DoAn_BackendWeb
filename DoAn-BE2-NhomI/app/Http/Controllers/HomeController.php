<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy sản phẩm cho BANNER TRÁI (Từ Master: Thỏa mãn HOT + Mới tạo trong 7 ngày)
        $promoProduct = DB::table('products')
            ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
            ->where('product_images.is_primary', 1)
            ->where('products.is_hot', 1)
            ->where('products.created_at', '>=', now()->subDays(7))
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.created_at', 'desc')
            ->first();

        // 2. Lấy TẤT CẢ sản phẩm và dùng PHÂN TRANG (Từ Master: 16 sản phẩm/trang)
        $newProducts = DB::table('products')
            ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
            ->where('product_images.is_primary', 1)
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.created_at', 'desc')
            ->paginate(16);

        // 3. Lấy danh sách sản phẩm trending (Từ nhánh Trung/51_San_pham_Trending)
        // Ưu tiên sản phẩm is_trending = 1, nếu không có thì fallback sang is_hot và view_count
        $trendingProducts = DB::table('products')
            ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
            ->where('product_images.is_primary', 1)
            ->where('products.is_active', 1)
            ->where('products.is_trending', 1)
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.view_count', 'desc')
            ->limit(10)
            ->get();

        // Fallback: Nếu không có sản phẩm trending nào
        if ($trendingProducts->isEmpty()) {
            $trendingProducts = DB::table('products')
                ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
                ->where('product_images.is_primary', 1)
                ->where('products.is_active', 1)
                ->select('products.*', 'product_images.image_url')
                ->orderBy('products.is_hot', 'desc')
                ->orderBy('products.view_count', 'desc')
                ->limit(10)
                ->get();
        }

        // Trả về view với đầy đủ 3 biến: newProducts, trendingProducts, promoProduct
        return view('home.index', compact('newProducts', 'trendingProducts', 'promoProduct'));
    }

    public function detail($id)
    {
        // Hàm detail giống hệt nhau ở cả 2 nhánh nên giữ nguyên
        $product = Product::findOrFail($id);

        $image = DB::table('product_images')
            ->where('product_id', $id)
            ->where('is_primary', 1)
            ->first();

        $product->image_url = $image->image_url ?? null;

        $variants = DB::table('product_variants')
            ->where('product_id', $id)
            ->where('is_active', 1)
            ->get();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->limit(4)
            ->get();

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