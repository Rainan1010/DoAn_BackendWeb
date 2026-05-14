<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\ShippingAddress;

use Illuminate\Http\Request;
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
        /*
 |--------------------------------------------------------------------------
 | QUERY
 |--------------------------------------------------------------------------
 */
        $query = Order::with('items')

            ->where(
                'user_id',
                $userId
            );



        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */
        if (request()->status) {

            $query->where(
                'order_status',
                request()->status
            );
        }



        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */
        $orders = $query

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
    /*
    |--------------------------------------------------------------------------
    | CANCEL ORDER
    |--------------------------------------------------------------------------
    */
    public function cancel($id)
    {

        $order = Order::where(
            'user_id',
            Auth::id()
        )->findOrFail($id);



        // chỉ cho huỷ khi chưa giao
        if (
            !in_array(
                $order->order_status,
                ['pending', 'confirmed', 'processing']
            )
        ) {

            return back()->with(
                'error',
                'Không thể huỷ đơn hàng này'
            );
        }



        $order->update([

            'order_status' => 'cancelled'

        ]);



        return back()->with(
            'success',
            'Huỷ đơn hàng thành công'
        );
    }
    /*
    |--------------------------------------------------------------------------
    | REORDER
    |--------------------------------------------------------------------------
    */
    public function reorder($id)
    {

        $order = Order::with('items')

            ->where(
                'user_id',
                Auth::id()
            )

            ->findOrFail($id);



        /*
        |--------------------------------------------------------------------------
        | TẠO CART MỚI RIÊNG
        |--------------------------------------------------------------------------
        */
        $cart = [];



        $selectedCartIds = [];



        /*
        |--------------------------------------------------------------------------
        | LOOP ITEMS
        |--------------------------------------------------------------------------
        */
        foreach ($order->items as $item) {

            $variant = DB::table('product_variants')

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

                    'products.name',

                    'product_variants.variant_id',

                    'product_variants.attribute_values',

                    'product_images.image_url'

                )

                ->first();



            if (!$variant) {
                continue;
            }



            /*
            |--------------------------------------------------------------------------
            | CART KEY
            |--------------------------------------------------------------------------
            */
            $cartKey =
                'product_' .
                $variant->variant_id;



            /*
            |--------------------------------------------------------------------------
            | ADD CART
            |--------------------------------------------------------------------------
            */
            $cart[$cartKey] = [

                'product_id' =>
                    $variant->product_id,

                'variant_id' =>
                    $variant->variant_id,

                'name' =>
                    $variant->name,

                'quantity' =>
                    $item->quantity,

                'price' =>
                    $item->unit_price,

                'image' =>
                    $variant->image_url,

                'variant_name' =>
                    $variant->attribute_values
            ];



            /*
            |--------------------------------------------------------------------------
            | AUTO SELECT
            |--------------------------------------------------------------------------
            */
            $selectedCartIds[] =
                $cartKey;
        }



        /*
        |--------------------------------------------------------------------------
        | GHI ĐÈ CART
        |--------------------------------------------------------------------------
        */
        session()->put(
            'cart',
            $cart
        );



        session()->put(
            'selected_cart_ids',
            $selectedCartIds
        );



        /*
        |--------------------------------------------------------------------------
        | QUA THẲNG CHECKOUT
        |--------------------------------------------------------------------------
        */
        return redirect()

            ->route('checkout')

            ->with(
                'success',
                'Đang mua lại sản phẩm'
            );
    }
    /*
    |--------------------------------------------------------------------------
    | CHECKOUT PAGE - STEP 1
    |--------------------------------------------------------------------------
    */
    public function checkout()
    {

        /*
        |--------------------------------------------------------------------------
        | CART
        |--------------------------------------------------------------------------
        */
        $cart = session()->get('cart', []);

        $selectedCartIds = session()->get(
            'selected_cart_ids',
            []
        );

        /*
        |--------------------------------------------------------------------------
        | CHECKOUT ITEMS
        |--------------------------------------------------------------------------
        */
        $checkoutItems = [];

        foreach ($selectedCartIds as $id) {

            if (isset($cart[$id])) {

                $checkoutItems[$id] = $cart[$id];
            }
        }
        /*
        |--------------------------------------------------------------------------
        | SAVE SESSION
        |--------------------------------------------------------------------------
        */
        session([
            'checkout_items' =>
                $checkoutItems
        ]);
        /*
        |--------------------------------------------------------------------------
        | NO PRODUCT
        |--------------------------------------------------------------------------
        */
        if (empty($checkoutItems)) {

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Vui lòng chọn sản phẩm để thanh toán'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | ADDRESS
        |--------------------------------------------------------------------------
        */
        $addresses = ShippingAddress::where(
            'user_id',
            Auth::id()
        )

            ->orderByDesc(
                'is_default'
            )

            ->orderByDesc(
                'address_id'
            )

            ->get();
        /*
        |--------------------------------------------------------------------------
        | OLD CHECKOUT INFO
        |--------------------------------------------------------------------------
        */
        $oldInfo = session(
            'checkout_information',
            []
        );
        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */
        $subtotal = 0;

        foreach ($checkoutItems as $item) {

            $subtotal +=
                $item['price']
                * $item['quantity'];
        }

        $shippingFee = 30000;

        $discount = 0;

        $total =
            $subtotal
            + $shippingFee
            - $discount;

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */
        return view(

            'checkout.index',

            compact(

                'checkoutItems',

                'addresses',

                'subtotal',

                'shippingFee',

                'discount',

                'total',

                'oldInfo'
            )
        );
    }






    /*
    |--------------------------------------------------------------------------
    | SAVE INFORMATION
    |--------------------------------------------------------------------------
    */
    public function saveInformation(Request $request)
    {

        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        */
        $request->validate([

            'full_name' =>
                'required',

            'phone' =>
                'required',

            'delivery_type' =>
                'required',
        ]);





        /*
        |--------------------------------------------------------------------------
        | ADDRESS DATA
        |--------------------------------------------------------------------------
        */
        $province = null;

        $district = null;

        $ward = null;

        $streetAddress = null;





        /*
        |--------------------------------------------------------------------------
        | GIAO TẬN NƠI
        |--------------------------------------------------------------------------
        */
        if (
            $request->delivery_type
            == 'home'
        ) {

            /*
            |--------------------------------------------------------------------------
            | ĐỊA CHỈ ĐÃ LƯU
            |--------------------------------------------------------------------------
            */
            if (
                $request->address_type
                == 'saved'
            ) {

                $address =
                    ShippingAddress::where(

                        'address_id',

                        $request->shipping_address_id

                    )

                        ->where(
                            'user_id',
                            Auth::id()
                        )

                        ->first();





                if (!$address) {

                    return back()->with(
                        'error',
                        'Vui lòng chọn địa chỉ'
                    );
                }






                $province =
                    $address->province;

                $district =
                    $address->district;

                $ward =
                    $address->ward;

                $streetAddress =
                    $address->street_address;
            }






            /*
            |--------------------------------------------------------------------------
            | ĐỊA CHỈ MỚI
            |--------------------------------------------------------------------------
            */ else {

                $request->validate([

                    'province' =>
                        'required',

                    'district' =>
                        'required',

                    'ward' =>
                        'required',

                    'street_address' =>
                        'required',
                ]);





                $province =
                    $request->province;

                $district =
                    $request->district;

                $ward =
                    $request->ward;

                $streetAddress =
                    $request->street_address;
            }
        }






        /*
        |--------------------------------------------------------------------------
        | SAVE SESSION
        |--------------------------------------------------------------------------
        */
        session([

            'checkout_information' => [

                'delivery_type' =>
                    $request->delivery_type,

                'address_type' =>
                    $request->address_type,

                'shipping_address_id' =>
                    $request->shipping_address_id,

                'pickup_store' =>
                    $request->pickup_store,

                'full_name' =>
                    $request->full_name,

                'phone' =>
                    $request->phone,

                'province' =>
                    $province,

                'district' =>
                    $district,

                'ward' =>
                    $ward,

                'street_address' =>
                    $streetAddress,

                'note' =>
                    $request->note,
            ]
        ]);





        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */
        return redirect()->route(
            'checkout.payment'
        );
    }





    /*
    |--------------------------------------------------------------------------
    | PAYMENT PAGE - STEP 2
    |--------------------------------------------------------------------------
    */
    public function payment()
    {

        /*
|--------------------------------------------------------------------------
| CHECKOUT ITEMS
|--------------------------------------------------------------------------
*/
        $checkoutItems = session()->get(
            'checkout_items',
            []
        );

        /*
        |--------------------------------------------------------------------------
        | EMPTY
        |--------------------------------------------------------------------------
        */
        if (empty($checkoutItems)) {

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Không có sản phẩm'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */
        $subtotal = 0;

        foreach ($checkoutItems as $item) {

            $subtotal +=
                $item['price']
                * $item['quantity'];
        }

        $shippingFee = 30000;

        $discount = 0;

        $total =
            $subtotal
            + $shippingFee
            - $discount;

        /*
        |--------------------------------------------------------------------------
        | INFORMATION
        |--------------------------------------------------------------------------
        */
        $info = session(
            'checkout_information'
        );
        /*
        |--------------------------------------------------------------------------
        | CHECK INFO
        |--------------------------------------------------------------------------
        */
        if (!$info) {

            return redirect()

                ->route('checkout')

                ->with(
                    'error',
                    'Vui lòng nhập thông tin giao hàng'
                );
        }
        /*
 |--------------------------------------------------------------------------
 | ADDRESSES
 |--------------------------------------------------------------------------
 */
        $addresses =
            ShippingAddress::where(
                'user_id',
                Auth::id()
            )->get();





        return view(

            'checkout.payment',

            compact(

                'checkoutItems',

                'info',

                'addresses',

                'subtotal',

                'shippingFee',

                'discount',

                'total'
            )
        );
    }







    /*
    |--------------------------------------------------------------------------
    | STORE ORDER
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {

        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        */
        $request->validate([

            'payment_method' =>
                'required',
        ]);





        /*
        |--------------------------------------------------------------------------
        | GET SESSION INFO
        |--------------------------------------------------------------------------
        */
        $info = session(
            'checkout_information'
        );





        /*
        |--------------------------------------------------------------------------
        | CHECK INFO
        |--------------------------------------------------------------------------
        */
        if (!$info) {

            return redirect()

                ->route('checkout')

                ->with(
                    'error',
                    'Vui lòng nhập thông tin giao hàng'
                );
        }






        /*
      |--------------------------------------------------------------------------
      | CART
      |--------------------------------------------------------------------------
      */
        $cart = session()->get(
            'cart',
            []
        );

        /*
        |--------------------------------------------------------------------------
        | SELECTED IDS
        |--------------------------------------------------------------------------
        */
        $selectedCartIds = session()->get(
            'selected_cart_ids',
            []
        );

        /*
        |--------------------------------------------------------------------------
        | CHECKOUT ITEMS
        |--------------------------------------------------------------------------
        */
        $checkoutItems = session()->get(
            'checkout_items',
            []
        );





        /*
        |--------------------------------------------------------------------------
        | EMPTY
        |--------------------------------------------------------------------------
        */
        if (empty($checkoutItems)) {

            return redirect()

                ->route('cart.index')

                ->with(
                    'error',
                    'Không có sản phẩm để thanh toán'
                );
        }






        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            */
            $subtotal = 0;

            foreach ($checkoutItems as $item) {

                $subtotal +=
                    $item['price']
                    * $item['quantity'];
            }

            $shippingFee = 30000;

            $discount = 0;

            $total =
                $subtotal
                + $shippingFee
                - $discount;






            /*
            |--------------------------------------------------------------------------
            | SHIPPING ADDRESS
            |--------------------------------------------------------------------------
            */
            $shippingAddressId = null;





            /*
            |--------------------------------------------------------------------------
            | GIAO TẬN NƠI
            |--------------------------------------------------------------------------
            */
            if (
                $info['delivery_type']
                == 'home'
            ) {

                /*
                |--------------------------------------------------------------------------
                | ĐỊA CHỈ ĐÃ LƯU
                |--------------------------------------------------------------------------
                */
                if (
                    $info['address_type']
                    == 'saved'
                ) {

                    $shippingAddressId =
                        $info['shipping_address_id'];
                }






                /*
                |--------------------------------------------------------------------------
                | ĐỊA CHỈ MỚI
                |--------------------------------------------------------------------------
                */ elseif (
                    $info['address_type']
                    == 'new'
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | CHECK EXIST ADDRESS
                    |--------------------------------------------------------------------------
                    */
                    $existAddress =
                        ShippingAddress::where(
                            'user_id',
                            Auth::id()
                        )

                            ->where(
                                'province',
                                $info['province']
                            )

                            ->where(
                                'district',
                                $info['district']
                            )

                            ->where(
                                'ward',
                                $info['ward']
                            )

                            ->where(
                                'street_address',
                                $info['street_address']
                            )

                            ->first();






                    /*
                    |--------------------------------------------------------------------------
                    | EXIST
                    |--------------------------------------------------------------------------
                    */
                    if ($existAddress) {

                        $shippingAddressId =
                            $existAddress->address_id;
                    }






                    /*
                    |--------------------------------------------------------------------------
                    | CREATE NEW ADDRESS
                    |--------------------------------------------------------------------------
                    */ else {

                        $address =
                            ShippingAddress::create([

                                'user_id' =>
                                    Auth::id(),

                                'full_name' =>
                                    $info['full_name'],

                                'phone' =>
                                    $info['phone'],

                                'province' =>
                                    $info['province'],

                                'district' =>
                                    $info['district'],

                                'ward' =>
                                    $info['ward'],

                                'street_address' =>
                                    $info['street_address'],

                                'is_default' =>
                                    0,
                            ]);

                        $shippingAddressId =
                            $address->address_id;
                    }
                }
            }






            /*
            |--------------------------------------------------------------------------
            | CREATE ORDER
            |--------------------------------------------------------------------------
            */
            $order = Order::create([

                'user_id' =>
                    Auth::id(),

                'shipping_address_id' =>
                    $shippingAddressId,

                'voucher_id' =>
                    null,

                'order_code' =>
                    'ORD-' . time(),

                'subtotal' =>
                    $subtotal,

                'shipping_fee' =>
                    $shippingFee,

                'discount_amount' =>
                    $discount,

                'total_amount' =>
                    $total,

                'payment_method' =>
                    $request->payment_method,

                'payment_status' =>

                    $request->payment_method == 'cod'
                    ? 'pending'
                    : 'paid',

                'order_status' =>
                    'pending',

                'cancel_reason' =>
                    null,

                'paid_at' =>

                    $request->payment_method != 'cod'
                    ? now()
                    : null,
            ]);






            /*
            |--------------------------------------------------------------------------
            | CREATE ORDER ITEMS
            |--------------------------------------------------------------------------
            */
            foreach ($checkoutItems as $item) {

                $variant =
                    ProductVariant::find(
                        $item['variant_id']
                    );





                /*
                |--------------------------------------------------------------------------
                | CHECK VARIANT
                |--------------------------------------------------------------------------
                */
                if (!$variant) {

                    throw new \Exception(
                        'Biến thể sản phẩm không tồn tại'
                    );
                }






                /*
                |--------------------------------------------------------------------------
                | CHECK STOCK
                |--------------------------------------------------------------------------
                */
                if (
                    $variant->stock_quantity
                    < $item['quantity']
                ) {

                    throw new \Exception(
                        'Sản phẩm '
                        . $item['name']
                        . ' không đủ tồn kho'
                    );
                }






                /*
                |--------------------------------------------------------------------------
                | CREATE ORDER ITEM
                |--------------------------------------------------------------------------
                */
                OrderItem::create([

                    'order_id' =>
                        $order->order_id,

                    'variant_id' =>
                        $variant->variant_id,

                    'product_name' =>
                        $item['name'],

                    'variant_info' =>
                        $item['variant_name']
                        ?? null,

                    'unit_price' =>
                        $item['price'],

                    'quantity' =>
                        $item['quantity'],

                    'subtotal' =>

                        $item['price']
                        * $item['quantity'],
                ]);






                /*
                |--------------------------------------------------------------------------
                | TRỪ TỒN KHO
                |--------------------------------------------------------------------------
                */
                $variant->decrement(

                    'stock_quantity',

                    $item['quantity']
                );
            }






            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */
            Payment::create([

                'order_id' =>
                    $order->order_id,

                'gateway' =>
                    $request->payment_method,

                'transaction_id' =>

                    strtoupper(
                        $request->payment_method
                    )

                    . '-'

                    . time(),

                'amount' =>
                    $total,

                'status' =>

                    $request->payment_method == 'cod'
                    ? 'pending'
                    : 'success',
            ]);






            /*
            |--------------------------------------------------------------------------
            | REMOVE CART
            |--------------------------------------------------------------------------
            */
            foreach ($selectedCartIds as $id) {

                unset($cart[$id]);
            }






            /*
            |--------------------------------------------------------------------------
            | UPDATE CART
            |--------------------------------------------------------------------------
            */
            session()->put(
                'cart',
                $cart
            );






            /*
            |--------------------------------------------------------------------------
            | CLEAR SESSION
            |--------------------------------------------------------------------------
            */
            session()->forget(
                'selected_cart_ids'
            );

            session()->forget(
                'checkout_information'
            );

            session()->forget(
                'checkout_items'
            );



            DB::commit();






            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */
            return redirect()

                ->route('order.history')

                ->with(

                    'success_order',

                    [

                        'code' =>
                            $order->order_code,

                        'total' =>
                            $order->total_amount
                    ]
                );

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }







    /*
    |--------------------------------------------------------------------------
    | SUCCESS
    |--------------------------------------------------------------------------
    */
    public function success($id)
    {

        $order = Order::with('items')

            ->where(
                'user_id',
                Auth::id()
            )

            ->findOrFail($id);

        return view(
            'checkout.success',
            compact('order')
        );
    }



}