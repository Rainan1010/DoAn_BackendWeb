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
            ->leftJoin('product_images', function($join) {
                $join->on('products.product_id', '=', 'product_images.product_id')
                     ->where('product_images.is_primary', 1);
            })
            ->where('products.is_hot', 1)
            ->where('products.created_at', '>=', now()->subDays(7))
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.created_at', 'desc')
            ->first();

        // 2. Lấy TẤT CẢ sản phẩm và dùng PHÂN TRANG (Từ Master: 16 sản phẩm/trang)
        $newProducts = DB::table('products')
            ->leftJoin('product_images', function($join) {
                $join->on('products.product_id', '=', 'product_images.product_id')
                     ->where('product_images.is_primary', 1);
            })
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.created_at', 'desc')
            ->paginate(16);

        // 3. Lấy danh sách sản phẩm trending: ưu tiên is_trending, bổ sung top view_count cho đủ 20
        $limit = 20;

        // Bước 1: Lấy các sản phẩm is_trending = 1
        $trendingProducts = DB::table('products')
            ->leftJoin('product_images', function($join) {
                $join->on('products.product_id', '=', 'product_images.product_id')
                     ->where('product_images.is_primary', 1);
            })
            ->where('products.is_active', 1)
            ->where('products.is_trending', 1)
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.view_count', 'desc')
            ->limit($limit)
            ->get();

        // Bước 2: Nếu chưa đủ 20, bổ sung top view_count (tránh trùng)
        $remaining = $limit - $trendingProducts->count();
        if ($remaining > 0) {
            $existingIds = $trendingProducts->pluck('product_id')->toArray();

            $topViewProducts = DB::table('products')
                ->leftJoin('product_images', function($join) {
                    $join->on('products.product_id', '=', 'product_images.product_id')
                         ->where('product_images.is_primary', 1);
                })
                ->where('products.is_active', 1)
                ->when(!empty($existingIds), fn($q) => $q->whereNotIn('products.product_id', $existingIds))
                ->select('products.*', 'product_images.image_url')
                ->orderBy('products.view_count', 'desc')
                ->limit($remaining)
                ->get();

            $trendingProducts = $trendingProducts->concat($topViewProducts);
        }

        // Trả về view với đầy đủ 3 biến: newProducts, trendingProducts, promoProduct
        return view('home.index', compact('newProducts', 'trendingProducts', 'promoProduct'));
    }

    public function detail($id)
    {
        $product = Product::findOrFail($id);

        // Tăng view_count mỗi khi có người truy cập trang chi tiết
        Product::where('product_id', $id)->increment('view_count');

        $images = DB::table('product_images')
            ->where('product_id', $id)
            ->orderBy('sort_order', 'asc')
            ->get();

        $image = $images->where('is_primary', 1)->first() ?? $images->first();

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

        // 🔥 Lấy đánh giá
        $reviews = \App\Models\Review::with(['user', 'images'])
            ->where('product_id', $id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('products.product_detail', compact(
            'product',
            'images',
            'variants',
            'relatedProducts',
            'reviews'
        ));
    }
}