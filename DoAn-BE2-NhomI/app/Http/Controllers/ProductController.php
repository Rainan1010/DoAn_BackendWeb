<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function searchAjax(Request $request)
    {
        $query = $request->get('query');

        // Lọc sản phẩm theo tên khớp với từ khóa
        $products = DB::table('products')
            ->where('name', 'LIKE', "%{$query}%")
            ->select('product_id', 'name', 'base_price') // Chỉ lấy các cột cần thiết để tối ưu tốc độ
            ->limit(6) // Giới hạn 6 kết quả để bảng gợi ý không quá dài
            ->get();

        // Trả về dữ liệu dạng JSON cho file search.js xử lý
        return response()->json($products);
    }
    public function show($id)
    {
        $product = DB::table('products')
            ->where('product_id', $id)
            ->first();

        if (!$product) {
            abort(404);
        }

        $images = DB::table('product_images')
            ->where('product_id', $id)
            ->get();

        $variants = DB::table('product_variants')
            ->where('product_id', $id)
            ->get();

        $relatedProducts = DB::table('products')
            ->leftJoin('product_images', function ($join) {
                $join->on('products.product_id', '=', 'product_images.product_id')
                    ->where('product_images.is_primary', 1);
            })
            ->where('products.category_id', $product->category_id)
            ->where('products.product_id', '!=', $id)
            ->where('products.is_active', 1)
            ->select(
                'products.*',
                'product_images.image_url'
            )
            ->limit(10)
            ->get();

        return view(
            'products.product_detail',
            compact(
                'product',
                'images',
                'variants',
                'relatedProducts'
            )
        );
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $hasPurchased = DB::table('orders')
            ->join('order_items', 'orders.order_id', '=', 'order_items.order_id')
            ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
            ->where('orders.user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('product_variants.product_id', $id)
            ->where('orders.order_status', 'delivered')
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đã mua và nhận hàng thành công.');
        }

        $review = new \App\Models\Review();
        $review->product_id = $id;
        $review->user_id = \Illuminate\Support\Facades\Auth::id();
        $review->order_item_id = 0; // Set default 0 để fix lỗi 'order_item_id' doesn't have a default value
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->status = 'pending';
        $review->created_at = now();
        $review->save();

        if ($request->hasFile('images')) {
            $sortOrder = 1;
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/reviews'), $filename);

                $reviewImage = new \App\Models\ReviewImage();
                $reviewImage->review_id = $review->review_id;
                // Lưu đường dẫn theo đúng format dự án (có thể có public/ hoặc không)
                // View đang dùng str_replace('public/', '', ...) nên lưu thế nào cũng được
                $reviewImage->image_url = 'images/reviews/' . $filename;
                $reviewImage->sort_order = $sortOrder++;
                $reviewImage->save();
            }
        }

        return back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt.');
    }

    public function category($slug)
    {
        $category = DB::table('categories')
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if (!$category) {
            abort(404);
        }

        // 1. Xác định danh mục cha chung và lấy các danh mục con trực thuộc danh mục cha đó
        $parentCategoryId = $category->parent_id ?: $category->category_id;

        $subCategories = DB::table('categories')
            ->where('parent_id', $parentCategoryId)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->get();

        // 2. Lấy danh sách ID danh mục cần truy vấn sản phẩm
        // Nếu danh mục hiện tại là danh mục cha, lấy sản phẩm của cha và tất cả các con
        if (empty($category->parent_id)) {
            $categoryIds = $subCategories->pluck('category_id')->toArray();
            $categoryIds[] = $category->category_id;
        } else {
            // Nếu là danh mục con, chỉ lấy sản phẩm của chính nó
            $categoryIds = [$category->category_id];
        }

        // 3. Lấy danh sách sản phẩm thuộc các danh mục trên
        $products = DB::table('products')
            ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
            ->where('product_images.is_primary', 1)
            ->whereIn('products.category_id', $categoryIds)
            ->where('products.is_active', 1)
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.created_at', 'desc')
            ->paginate(12);

        // 4. Lấy thông tin danh mục cha để làm nút quay lại "Tất cả" nếu đang ở danh mục con
        $parentCategory = null;
        if ($category->parent_id) {
            $parentCategory = DB::table('categories')
                ->where('category_id', $category->parent_id)
                ->first();
        }

        return view('products.category', compact('category', 'products', 'subCategories', 'parentCategory'));
    }

    public function promotions()
    {
        // Lấy các sản phẩm có is_hot = 1 (sản phẩm khuyến mãi)
        $products = DB::table('products')
            ->join('product_images', 'products.product_id', '=', 'product_images.product_id')
            ->where('product_images.is_primary', 1)
            ->where('products.is_hot', 1)
            ->where('products.is_active', 1)
            ->select('products.*', 'product_images.image_url')
            ->orderBy('products.created_at', 'desc')
            ->paginate(12);

        return view('products.promotions', compact('products'));
    }
}
