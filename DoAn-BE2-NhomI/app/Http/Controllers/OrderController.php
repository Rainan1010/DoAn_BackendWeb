<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Lịch sử đơn hàng
     */
    public function history()
    {
        // lấy user đang đăng nhập
        $userId = Auth::id();

        // lấy danh sách đơn hàng
        $orders = Order::with('items')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(5);

        // lấy ảnh sản phẩm cho từng item
        foreach ($orders as $order) {

            foreach ($order->items as $item) {

                $product = DB::table('product_variants')
                    ->join(
                        'products',
                        'products.product_id',
                        '=',
                        'product_variants.product_id'
                    )
                    ->leftJoin(
                        'product_images',
                        'product_images.product_id',
                        '=',
                        'products.product_id'
                    )
                    ->where('product_variants.variant_id', $item->variant_id)
                    ->where('product_images.is_primary', 1)
                    ->select(
                        'products.product_id',
                        'product_images.image_url'
                    )
                    ->first();

                $item->image_url = $product->image_url ?? null;
            }
        }

        return view('auth.orders.history', compact('orders'));
    }
}