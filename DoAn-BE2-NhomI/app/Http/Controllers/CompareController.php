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

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        $id = $request->input('product_id');

        $exists = DB::table('products')
            ->where('product_id', $id)
            ->exists();

        if (!$exists) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
            }
            return back()->with('error', 'Sản phẩm không tồn tại');
        }

        $compare = session()->get('compare_products', []);

        if (in_array($id, $compare)) {
            if ($request->ajax()) {
                return response()->json(['status' => 'exists', 'message' => 'Sản phẩm đã có trong danh sách so sánh', 'count' => count($compare)]);
            }
            return back()->with('info', 'Sản phẩm đã có trong danh sách so sánh');
        }

        if (count($compare) >= 3) {
            if ($request->ajax()) {
                return response()->json(['status' => 'full', 'message' => 'Chỉ được so sánh tối đa 3 sản phẩm'], 422);
            }
            return back()->with('error', 'Chỉ được so sánh tối đa 3 sản phẩm');
        }

        $compare[] = $id;
        session(['compare_products' => $compare]);

        if ($request->ajax()) {
            return response()->json(['status' => 'ok', 'message' => 'Đã thêm vào danh sách so sánh', 'count' => count($compare)]);
        }

        return back()->with('success', 'Đã thêm vào danh sách so sánh');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        $id = $request->input('product_id');
        $compare = session()->get('compare_products', []);

        $compare = array_values(array_diff($compare, [$id]));
        session(['compare_products' => $compare]);

        if ($request->ajax()) {
            return response()->json(['status' => 'ok', 'message' => 'Đã xóa khỏi danh sách so sánh', 'count' => count($compare)]);
        }

        return back()->with('success', 'Đã xóa khỏi danh sách so sánh');
    }

    public function clear(Request $request)
    {
        session()->forget('compare_products');

        if ($request->ajax()) {
            return response()->json(['status' => 'ok', 'message' => 'Danh sách so sánh đã được xóa']);
        }

        return back()->with('success', 'Danh sách so sánh đã được xóa');
    }

    public function index()
    {
        $ids = session()->get('compare_products', []);

        $products = [];

        if (!empty($ids)) {
            $rows = DB::table('products')
                ->leftJoin('product_images', function ($join) {
                    $join->on('products.product_id', '=', 'product_images.product_id')
                        ->where('product_images.is_primary', 1);
                })
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.brand_id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
                ->whereIn('products.product_id', $ids)
                ->select('products.*', 'product_images.image_url', 'brands.name as brand_name', 'categories.name as category_name')
                ->get()
                ->keyBy('product_id');

            foreach ($ids as $id) {
                if (isset($rows[$id])) {
                    $products[] = $rows[$id];
                }
            }
        }

        return view('compare.index', compact('products'));
    }
}
