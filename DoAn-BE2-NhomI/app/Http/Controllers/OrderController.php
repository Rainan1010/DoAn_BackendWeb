<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\ShippingAddress;
use App\Models\Cart;
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

        /*
        |--------------------------------------------------------------------------
        | GET ORDER
        |--------------------------------------------------------------------------
        */
        $order = Order::with('items')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | CLEAR OLD SESSION
        |--------------------------------------------------------------------------
        */
        session()->forget([

            'checkout_items',

            'selected_cart_ids',

            'checkout_information',

            'checkout_total',

            'shipping_fee',

            'discount_amount',

            'coupon',

            'is_reorder'
        ]);

        /*
        |--------------------------------------------------------------------------
        | CHECKOUT ITEMS
        |--------------------------------------------------------------------------
        */
        $checkoutItems = [];

        $subtotal = 0;

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

            $image = $product->image_url
                ?? 'images/default-product.png';

            $checkoutItems[] = [

                'product_id' =>
                    $product->product_id ?? null,

                'variant_id' =>
                    $item->variant_id,

                'name' =>
                    $item->product_name,

                'variant_name' =>
                    $item->variant_info,

                'quantity' =>
                    $item->quantity,

                'price' =>
                    $item->unit_price,

                'image' =>
                    $image,
            ];

            $subtotal +=
                $item->unit_price
                * $item->quantity;
        }

        /*
        |--------------------------------------------------------------------------
        | SHIPPING
        |--------------------------------------------------------------------------
        */
        $shippingFee = session(
            'shipping_fee',
            30000
        );

        $discount = session(
            'discount_amount',
            0
        );
        $vat = $subtotal * 0.1;

        $total =
            $subtotal
            + $shippingFee
            + $vat
            - $discount;

        /*
        |--------------------------------------------------------------------------
        | SHIPPING ADDRESS
        |--------------------------------------------------------------------------
        */
        $shippingAddress = ShippingAddress::where(
            'user_id',
            Auth::id()
        )

            ->orderByDesc('address_id')

            ->first();

        /*
        |--------------------------------------------------------------------------
        | AUTO CHECKOUT INFO
        |--------------------------------------------------------------------------
        */
        if ($shippingAddress) {

            session([

                'checkout_information' => [

                    'delivery_type' => 'home',

                    'address_type' => 'saved',

                    'shipping_address_id' =>
                        $shippingAddress->address_id,

                    'full_name' =>
                        $shippingAddress->full_name,

                    'phone' =>
                        $shippingAddress->phone,

                    'province' =>
                        $shippingAddress->province,

                    'district' =>
                        $shippingAddress->district,

                    'ward' =>
                        $shippingAddress->ward,

                    'street_address' =>
                        $shippingAddress->street_address,

                    'note' => null,
                ]
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE SESSION
        |--------------------------------------------------------------------------
        */
        session([

            'checkout_items' =>
                $checkoutItems,

            'shipping_fee' =>
                $shippingFee,

            'discount_amount' =>
                $discount,

            'checkout_total' =>
                $total,

            'is_reorder' =>
                true
        ]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('checkout.payment');
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
        | LOAD CART FROM DATABASE
        |--------------------------------------------------------------------------
        */
        $cart = [];

        $userCart = Cart::with(['items.variant.product'])
            ->where('user_id', Auth::id())
            ->first();

        if ($userCart) {

            foreach ($userCart->items as $item) {

                $variant = $item->variant;

                if (!$variant || !$variant->product) {
                    continue;
                }

                $product = $variant->product;

                $cartKey =
                    $product->product_id .
                    '_variant_' .
                    $variant->variant_id;

                $image = DB::table('product_images')
                    ->where('product_id', $product->product_id)
                    ->where('is_primary', 1)
                    ->value('image_url');

                $cart[$cartKey] = [

                    'product_id' => $product->product_id,

                    'variant_id' => $variant->variant_id,

                    'name' => $product->name,

                    'variant_name' => $variant->attribute_values,

                    'quantity' => $item->quantity,

                    'price' => $item->price,

                    'image' => $image ?? 'images/default-product.png',
                ];
            }
        }

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
        | AUTO SELECT ALL IF EMPTY
        |--------------------------------------------------------------------------
        */
        if (empty($selectedCartIds)) {

            $selectedCartIds = array_keys($cart);

            session()->put(
                'selected_cart_ids',
                $selectedCartIds
            );
        }

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
        | STILL EMPTY
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

        /*
        |--------------------------------------------------------------------------
        | SAVE SESSION
        |--------------------------------------------------------------------------
        */
        session([
            'checkout_items' => $checkoutItems
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADDRESS
        |--------------------------------------------------------------------------
        */
        $addresses = ShippingAddress::where(
            'user_id',
            Auth::id()
        )
            ->orderByDesc('is_default')
            ->orderByDesc('address_id')
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
        $checkoutItems = session()->get(
            'checkout_items',
            []
        );

        if (empty($checkoutItems)) {

            return redirect()

                ->route('cart.index')

                ->with(
                    'error',
                    'Không có sản phẩm'
                );
        }

        $subtotal = 0;

        foreach ($checkoutItems as $item) {

            $subtotal +=
                $item['price']
                * $item['quantity'];
        }

        $shippingFee = 30000;

        $discount = 0;

        $vat = $subtotal * 0.1;

        $total =
            $subtotal
            + $shippingFee
            + $vat
            - $discount;

        $info = session(
            'checkout_information',
            []
        );

        $addresses = ShippingAddress::where(
            'user_id',
            Auth::id()
        )->get();

        return view(
            'checkout.payment',
            compact(
                'checkoutItems',
                'subtotal',
                'shippingFee',
                'discount',
                'vat',
                'total',
                'info',
                'addresses'
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
        $request->validate([

            'payment_method' => 'required',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CHECKOUT INFO
        |--------------------------------------------------------------------------
        */
        $info = session('checkout_information');

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
        | CHECKOUT ITEMS
        |--------------------------------------------------------------------------
        */
        $checkoutItems = session()->get(
            'checkout_items',
            []
        );

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

            $vat = $subtotal * 0.1;

            $total =
                $subtotal
                + $shippingFee
                + $vat
                - $discount;

            /*
            |--------------------------------------------------------------------------
            | SHIPPING ADDRESS
            |--------------------------------------------------------------------------
            */
            $shippingAddressId = null;

            if ($info['delivery_type'] == 'home') {

                if ($info['address_type'] == 'saved') {

                    $shippingAddressId =
                        $info['shipping_address_id'];

                } else {

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

                    if ($existAddress) {

                        $shippingAddressId =
                            $existAddress->address_id;

                    } else {

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
                    'pending',

                'order_status' =>
                    'pending',

                'cancel_reason' =>
                    null,

                'paid_at' =>
                    null,
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

                if (!$variant) {

                    throw new \Exception(
                        'Biến thể sản phẩm không tồn tại'
                    );
                }

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

                OrderItem::create([

                    'order_id' =>
                        $order->order_id,

                    'variant_id' =>
                        $variant->variant_id,

                    'product_name' =>
                        $item['name'],

                    'variant_info' =>

                        is_array($item['variant_name'] ?? null)

                        ? implode(
                            ' - ',
                            $item['variant_name']
                        )

                        : (
                            $item['variant_name']
                            ?? null
                        ),

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
                    'pending',
            ]);

            /*
            |--------------------------------------------------------------------------
            | MOMO
            |--------------------------------------------------------------------------
            */
            if ($request->payment_method == 'momo') {

                session([
                    'pending_order_id' =>
                        $order->order_id
                ]);

                DB::commit();

                return $this->momoPayment(
                    $total
                );
            }
            /*
           |--------------------------------------------------------------------------
           | VNPAY
           |--------------------------------------------------------------------------
           */
           if ($request->payment_method === 'vnpay') {

    DB::commit();

    return $this->vnpayPayment($order);
}
            /*
            |--------------------------------------------------------------------------
            | CLEAR SESSION
            |--------------------------------------------------------------------------
            */
            session()->forget(
                'checkout_items'
            );
            session()->forget(
                'is_reorder'
            );
            session()->forget(
                'selected_cart_ids'
            );

            session()->forget(
                'checkout_information'
            );

            session()->forget(
                'is_reorder'
            );

            /*
            |--------------------------------------------------------------------------
            | COD SUCCESS
            |--------------------------------------------------------------------------
            */
            DB::commit();

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


    /*
   |--------------------------------------------------------------------------
   | MOMO PAYMENT
   |--------------------------------------------------------------------------
   */
    public function momoPayment($amount)
    {

        $endpoint = env('MOMO_ENDPOINT');

        $partnerCode = env('MOMO_PARTNER_CODE');

        $accessKey = env('MOMO_ACCESS_KEY');

        $secretKey = env('MOMO_SECRET_KEY');

        $redirectUrl = env('MOMO_REDIRECT_URL');

        $ipnUrl = env('MOMO_IPN_URL');

        $orderInfo = "Thanh toan don hang";

        $amount = (string) $amount;

        $orderId = time() . "";

        $requestId = time() . "";

        $extraData = base64_encode("");

        $requestType = "captureWallet";

        /*
        |--------------------------------------------------------------------------
        | RAW HASH
        |--------------------------------------------------------------------------
        */
        $rawHash =
            "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $ipnUrl .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $partnerCode .
            "&redirectUrl=" . $redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . $requestType;

        /*
        |--------------------------------------------------------------------------
        | SIGNATURE
        |--------------------------------------------------------------------------
        */
        $signature = hash_hmac(
            "sha256",
            $rawHash,
            $secretKey
        );

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */
        $data = [

            "partnerCode" => $partnerCode,

            "partnerName" => "Test",

            "storeId" => "MomoTestStore",

            "requestId" => $requestId,

            "amount" => $amount,

            "orderId" => $orderId,

            "orderInfo" => $orderInfo,

            "redirectUrl" => $redirectUrl,

            "ipnUrl" => $ipnUrl,

            "lang" => "vi",

            "extraData" => $extraData,

            "requestType" => $requestType,

            "autoCapture" => true,

            "signature" => $signature
        ];

        $result = $this->execPostRequest(
            $endpoint,
            json_encode($data)
        );

        $jsonResult = json_decode(
            $result,
            true
        );

        if (isset($jsonResult['payUrl'])) {

            return redirect(
                $jsonResult['payUrl']
            );
        }

        dd($jsonResult);
    }

    /*
   |--------------------------------------------------------------------------
   | MOMO RETURN
   |--------------------------------------------------------------------------
   */
    public function momoReturn(Request $request)
    {

        $orderId = session(
            'pending_order_id'
        );

        $order = Order::find($orderId);

        /*
        |--------------------------------------------------------------------------
        | ORDER NOT FOUND
        |--------------------------------------------------------------------------
        */
        if (!$order) {

            return redirect('/')->with(

                'error',

                'Không tìm thấy đơn hàng'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT SUCCESS
        |--------------------------------------------------------------------------
        */
        if (

            $request->resultCode == 0

            ||

            empty($request->resultCode)

        ) {

            /*
            |--------------------------------------------------------------------------
            | UPDATE ORDER
            |--------------------------------------------------------------------------
            */
            $order->update([

                'payment_status' => 'paid',

                'order_status' => 'processing',

                'paid_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE PAYMENT
            |--------------------------------------------------------------------------
            */
            Payment::where(

                'order_id',

                $order->order_id

            )->update([

                        'status' => 'success'
                    ]);

            /*
            |--------------------------------------------------------------------------
            | CLEAR SESSION
            |--------------------------------------------------------------------------
            */
            Cart::where(
                'user_id',
                Auth::id()
            )->delete();
            session()->forget(
                'pending_order_id'
            );

            session()->forget(
                'selected_cart_ids'
            );

            session()->forget(
                'checkout_information'
            );

            session()->forget(
                'checkout_items'
            );

            /*
            |--------------------------------------------------------------------------
            | SUCCESS REDIRECT
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
        }
        foreach ($order->items as $item) {

            ProductVariant::where(
                'variant_id',
                $item->variant_id
            )->increment(
                    'stock_quantity',
                    $item->quantity
                );
        }
        /*
        |--------------------------------------------------------------------------
        | PAYMENT FAIL
        |--------------------------------------------------------------------------
        */
        $order->update([

            'payment_status' => 'pending',

            'order_status' => 'pending'
        ]);

        Payment::where(

            'order_id',

            $order->order_id

        )->update([

                    'status' => 'pending'
                ]);

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
    }
    private function execPostRequest($url, $data)
    {

        $ch = curl_init($url);

        curl_setopt_array($ch, [

            CURLOPT_CUSTOMREQUEST => "POST",

            CURLOPT_POSTFIELDS => $data,

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_SSL_VERIFYPEER => false,

            CURLOPT_SSL_VERIFYHOST => false,

            CURLOPT_HTTPHEADER => [

                'Content-Type: application/json',

                'Content-Length: ' . strlen($data)
            ]
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {

            dd(curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }



// =========================
// VNPAY PAYMENT
// =========================

public function vnpayPayment($order)
{
    $vnp_Url = env('VNP_URL'); // Lấy từ .env của bạn
    $vnp_Returnurl = env('VNP_RETURN_URL'); // http://localhost:8000/vnpay/return

    // Các tham số giả lập y hệt tài liệu VNPAY
    $vnp_Params = [
        "vnp_Amount" => $order->total_amount * 100,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => request()->ip(),
        "vnp_Locale" => "vn",
        "vnp_OrderInfo" => "Thanh toan don hang #" . $order->order_code,
        "vnp_OrderType" => "billpayment",
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TmnCode" => env('VNP_TMN_CODE'),
        "vnp_TxnRef" => $order->order_code, // Dùng mã đơn hàng để đối soát
        "vnp_Version" => "2.1.0",
    ];

    // Thay vì tạo chữ ký thật (Hash), mình chỉ cần Build Query
    $queryString = http_build_query($vnp_Params);
    
    // Giả lập: thay vì đi đến cổng VNPAY thật, mình đi đến một view trung gian
    // Bạn có thể redirect thẳng về vnp_ReturnUrl nếu muốn bỏ qua bước nhấn nút
    return redirect()->route('vnpay.mock_portal', $vnp_Params);
}

public function vnpayMockPortal(Request $request)
{
    // Đây là trang giao diện giả lập cổng VNPAY
    // Bạn có thể cho người dùng thấy số tiền và nút "Xác nhận thanh toán"
    return "
        <div style='max-width:500px; margin:50px auto; text-align:center; font-family:Arial; border:1px solid #ddd; padding:20px;'>
            <img src='https://sandbox.vnpayment.vn/paymentv2/Images/brands/logo.svg' width='150'>
            <h2>CỔNG THANH TOÁN (GIẢ LẬP)</h2>
            <p>Đơn hàng: <b>#{$request->vnp_TxnRef}</b></p>
            <p>Số tiền: <b style='color:red;'>" . number_format($request->vnp_Amount / 100) . " VNĐ</b></p>
            <hr>
            <form action='{$request->vnp_ReturnUrl}' method='GET'>
                <!-- Gửi ngược lại toàn bộ data VNPAY gửi sang -->
                " . collect($request->all())->map(fn($v, $k) => "<input type='hidden' name='{$k}' value='{$v}'>")->implode('') . "
                
                <button name='vnp_ResponseCode' value='00' style='background:#0056b3; color:white; border:none; padding:10px 20px; cursor:pointer;'>
                    XÁC NHẬN THANH TOÁN THÀNH CÔNG
                </button>
                <br><br>
                <button name='vnp_ResponseCode' value='24' style='background:none; border:none; color:red; cursor:pointer; text-decoration:underline;'>
                    Hủy giao dịch
                </button>
            </form>
        </div>
    ";
}
// =========================
// VNPAY RETURN
// =========================

public function vnpayReturn(Request $request)
{
    $orderCode = $request->vnp_TxnRef;
    $responseCode = $request->vnp_ResponseCode; // '00' là thành công

    $order = Order::where('order_code', $orderCode)->first();

    if ($responseCode == '00') {
        // Cập nhật Database
        $order->update([
            'payment_status' => 'paid',
            'order_status' => 'pending',
            'paid_at' => now()
        ]);

        // Xóa giỏ hàng, trừ kho ở đây...
        session()->forget(['checkout_items', 'selected_cart_ids']);

        return redirect()->route('orders.history')->with('success', 'Thanh toán qua VNPAY thành công!');
    }

    return redirect()->route('checkout.payment')->with('error', 'Thanh toán thất bại hoặc bị hủy.');
}
}