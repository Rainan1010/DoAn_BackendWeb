<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    /**
     * =====================================================
     * LỊCH SỬ ĐƠN HÀNG
     * =====================================================
     */
    public function history()
    {

        // =================================================
        // USER ĐANG ĐĂNG NHẬP
        // =================================================
        $userId = Auth::id();



        // =================================================
        // LẤY DANH SÁCH ĐƠN HÀNG
        // =================================================
        $orders = Order::with('items')

            ->where(
                'user_id',
                $userId
            )

            ->orderByDesc(
                'created_at'
            )

            ->paginate(5);




        // =================================================
        // LẤY ẢNH CHO TỪNG ITEM
        // =================================================
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
                        function ($join) {

                            $join->on(
                                'product_images.product_id',
                                '=',
                                'products.product_id'
                            )

                                ->where(
                                    'product_images.is_primary',
                                    1
                                );

                        }
                    )

                    ->where(
                        'product_variants.variant_id',
                        $item->variant_id
                    )

                    ->select(
                        'products.product_id',
                        'product_images.image_url'
                    )

                    ->first();




                // =========================================
                // IMAGE URL
                // =========================================
                $item->image_url =
                    $product->image_url ?? null;

            }

        }




        // =================================================
        // RETURN VIEW
        // =================================================
        return view(
            'auth.orders.history',
            compact('orders')
        );

    }







    /**
     * =====================================================
     * CHI TIẾT ĐƠN HÀNG
     * =====================================================
     */
    public function detail($id)
    {
        $order = Order::with('items')
            ->where('user_id', Auth::id())
            ->findOrFail($id);



        // =================================================
        // LẤY ẢNH CHO ITEM
        // =================================================
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
                    function ($join) {

                        $join->on(
                            'product_images.product_id',
                            '=',
                            'products.product_id'
                        )

                            ->where(
                                'product_images.is_primary',
                                1
                            );

                    }
                )

                ->where(
                    'product_variants.variant_id',
                    $item->variant_id
                )

                ->select(
                    'products.product_id',
                    'product_images.image_url'
                )

                ->first();

            $item->image_url =
                $product->image_url ?? null;
        }



        // =================================================
        // LẤY ĐÚNG ĐỊA CHỈ CỦA ĐƠN HÀNG
        // =================================================
        $shippingAddress = ShippingAddress::where(
            'address_id',
            $order->shipping_address_id
        )->first();



        return view(
            'auth.orders.detail',
            compact(
                'order',
                'shippingAddress'
            )
        );
    }

}