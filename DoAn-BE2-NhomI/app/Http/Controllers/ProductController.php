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
        // 1. Lấy thông tin sản phẩm
        $product = DB::table('products')->where('product_id', $id)->first();

        if (!$product) {
            abort(404);
        }

        // 2. LẤY DANH SÁCH ẢNH (Đây là phần bị thiếu dẫn đến lỗi)
        $images = DB::table('product_images')->where('product_id', $id)->get();

        // 3. Lấy các biến thể
        $variants = DB::table('product_variants')->where('product_id', $id)->get();

        // 4. Truyền đầy đủ cả 3 biến sang View
        return view('products.show', compact('product', 'images', 'variants'));
    }
}
