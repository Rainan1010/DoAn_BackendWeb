<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ShippingFee;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\ShippingAddress;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * =====================================================
     * LỊCH SỬ ĐƠN HÀNG
     * =====================================================
     */
    public function history()
    {
        $userId = Auth::id();

        $query = Order::with('items')
            ->where('user_id', $userId);

        if (request()->status) {
            $query->where('order_status', request()->status);
        }

        $orders = $query
            ->orderByDesc('created_at')
            ->paginate(5);

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $product = DB::table('product_variants')
                    ->join('products', 'products.product_id', '=', 'product_variants.product_id')
                    ->leftJoin('product_images', function ($join) {
                        $join->on('product_images.product_id', '=', 'products.product_id')
                            ->where('product_images.is_primary', 1);
                    })
                    ->where('product_variants.variant_id', $item->variant_id)
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

        foreach ($order->items as $item) {
            $product = DB::table('product_variants')
                ->join('products', 'products.product_id', '=', 'product_variants.product_id')
                ->leftJoin('product_images', function ($join) {
                    $join->on('product_images.product_id', '=', 'products.product_id')
                        ->where('product_images.is_primary', 1);
                })
                ->where('product_variants.variant_id', $item->variant_id)
                ->select(
                    'products.product_id',
                    'product_images.image_url'
                )
                ->first();

            $item->image_url = $product->image_url ?? null;
        }

        $shippingAddress = ShippingAddress::where(
            'address_id',
            $order->shipping_address_id
        )->first();

        return view(
            'auth.orders.detail',
            compact('order', 'shippingAddress')
        );
    }

    /**
     * =====================================================
     * IN HÓA ĐƠN PDF
     * =====================================================
     */
    public function invoicePdf($id)
    {
        $orderQuery = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.user_id')
            ->leftJoin('shipping_addresses', 'orders.shipping_address_id', '=', 'shipping_addresses.address_id')
            ->select(
                'orders.*',
                'users.full_name as user_full_name',
                'users.email as user_email',
                'shipping_addresses.full_name as receiver_name',
                'shipping_addresses.phone',
                'shipping_addresses.province',
                'shipping_addresses.district',
                'shipping_addresses.ward',
                'shipping_addresses.street_address'
            )
            ->where('orders.order_id', $id);

        /*
         * Nếu là admin thì cho xem mọi hóa đơn.
         * Nếu là user thường thì chỉ xem đơn của chính mình.
         */
        $currentUser = Auth::user();

        if (!in_array($currentUser->role ?? null, ['admin', 'administrator'])) {
            $orderQuery->where('orders.user_id', Auth::id());
        }

        $order = $orderQuery->first();

        if (!$order) {
            abort(404, 'Không tìm thấy đơn hàng');
        }

        $items = DB::table('order_items')
            ->leftJoin('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.product_id')
            ->select(
                'order_items.*',
                'products.name as product_name_db',
                'product_variants.sku',
                'product_variants.attribute_values'
            )
            ->where('order_items.order_id', $id)
            ->get();

        $pdf = Pdf::loadView('orders.invoice_pdf', [
            'order' => $order,
            'items' => $items,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream(
            'hoa-don-' . ($order->order_code ?? $order->order_id) . '.pdf'
        );
    }

    /**
     * =====================================================
     * HỦY ĐƠN HÀNG
     * =====================================================
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|string|min:5|max:500',
        ], [
            'cancel_reason.required' => 'Vui lòng nhập lý do huỷ đơn.',
            'cancel_reason.min' => 'Lý do huỷ đơn cần có ít nhất 5 ký tự.',
            'cancel_reason.max' => 'Lý do huỷ đơn không được vượt quá 500 ký tự.',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::with('items')
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->findOrFail($id);

            if (!in_array($order->order_status, ['pending', 'confirmed', 'processing'])) {
                DB::rollBack();

                return back()->with(
                    'error',
                    'Không thể huỷ đơn hàng này'
                );
            }

            // Hoàn lại số lượng tồn kho
            foreach ($order->items as $item) {
                ProductVariant::where('variant_id', $item->variant_id)
                    ->increment('stock_quantity', $item->quantity);
            }

            // Hoàn trả lượt dùng Voucher nếu có
            if ($order->voucher_id) {
                DB::table('vouchers')
                    ->where('voucher_id', $order->voucher_id)
                    ->where('used_count', '>', 0)
                    ->decrement('used_count');
            }

            $shouldRefund = $order->payment_status === 'paid'
                || Payment::where('order_id', $order->order_id)
                    ->where('status', 'success')
                    ->whereIn('gateway', ['momo', 'vnpay'])
                    ->exists();

            Payment::where('order_id', $order->order_id)
                ->update([
                    'status' => $shouldRefund ? 'refunded' : 'failed',
                ]);

            $order->update([
                'order_status' => 'cancelled',
                'payment_status' => $shouldRefund ? 'refunded' : 'pending',
                'cancel_reason' => $request->cancel_reason,
            ]);

            DB::commit();

            $message = $shouldRefund
                ? 'Huỷ đơn hàng thành công. Đơn đã thanh toán sẽ được hoàn tiền về phương thức thanh toán ban đầu trong 3-7 ngày làm việc.'
                : 'Huỷ đơn hàng thành công. Đơn chưa thanh toán hoặc thanh toán COD nên không phát sinh hoàn tiền.';

            return back()->with(
                'success',
                $message
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(
                'error',
                'Có lỗi xảy ra khi huỷ đơn hàng: ' . $e->getMessage()
            );
        }
    }

    /**
     * =====================================================
     * MUA LẠI
     * =====================================================
     */
    public function reorder($id)
    {
        $order = Order::with('items')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        session()->forget([
            'checkout_items',
            'selected_cart_ids',
            'checkout_information',
            'checkout_total',
            'shipping_fee',
            'discount_amount',
            'coupon',
            'is_reorder',
        ]);

        $checkoutItems = [];
        $subtotal = 0;

        foreach ($order->items as $item) {
            $product = DB::table('product_variants')
                ->join('products', 'products.product_id', '=', 'product_variants.product_id')
                ->leftJoin('product_images', function ($join) {
                    $join->on('product_images.product_id', '=', 'products.product_id')
                        ->where('product_images.is_primary', 1);
                })
                ->where('product_variants.variant_id', $item->variant_id)
                ->select(
                    'products.product_id',
                    'product_images.image_url'
                )
                ->first();

            $image = $product->image_url ?? 'images/default-product.png';

            $checkoutItems[] = [
                'product_id' => $product->product_id ?? null,
                'variant_id' => $item->variant_id,
                'name' => $item->product_name,
                'variant_name' => $item->variant_info,
                'quantity' => $item->quantity,
                'price' => $item->unit_price,
                'image' => $image,
            ];

            $subtotal += $item->unit_price * $item->quantity;
        }

        $shippingFee = session('shipping_fee', 30000);
        $discount = session('discount_amount', 0);
        $vat = $subtotal * 0.1;

        $total = $subtotal + $shippingFee + $vat - $discount;

        $shippingAddress = ShippingAddress::where('user_id', Auth::id())
            ->orderByDesc('address_id')
            ->first();

        if ($shippingAddress) {
            session([
                'checkout_information' => [
                    'delivery_type' => 'home',
                    'address_type' => 'saved',
                    'shipping_address_id' => $shippingAddress->address_id,
                    'full_name' => $shippingAddress->full_name,
                    'phone' => $shippingAddress->phone,
                    'province' => $shippingAddress->province,
                    'district' => $shippingAddress->district,
                    'ward' => $shippingAddress->ward,
                    'street_address' => $shippingAddress->street_address,
                    'note' => null,
                ],
            ]);
        }

        session([
            'checkout_items' => $checkoutItems,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discount,
            'checkout_total' => $total,
            'is_reorder' => true,
        ]);

        return redirect()->route('checkout.payment');
    }

    /**
     * =====================================================
     * CHECKOUT STEP 1
     * =====================================================
     */
    public function checkout()
    {
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

                $cartKey = $product->product_id . '_variant_' . $variant->variant_id;

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

        $selectedCartIds = session()->get('selected_cart_ids', []);

        if (empty($selectedCartIds)) {
            $selectedCartIds = array_keys($cart);
            session()->put('selected_cart_ids', $selectedCartIds);
        }

        $checkoutItems = [];

        foreach ($selectedCartIds as $id) {
            if (isset($cart[$id])) {
                $checkoutItems[$id] = $cart[$id];
            }
        }

        if (empty($checkoutItems)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Không có sản phẩm để thanh toán');
        }

        session([
            'checkout_items' => $checkoutItems,
        ]);

        $addresses = ShippingAddress::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->orderByDesc('address_id')
            ->get();

        $oldInfo = session('checkout_information', []);

        $subtotal = 0;

        foreach ($checkoutItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $province = null;

        if (!empty($oldInfo['province'])) {
            $province = $oldInfo['province'];
        } elseif ($addresses->count()) {
            $province = $addresses->first()->province;
        }

        $shippingFee = $this->getShippingFeeByProvince($province);

        $discount = $this->getDiscountAmount($subtotal);

        $vat = $subtotal * 0.1;

        $total = $subtotal + $shippingFee + $vat - $discount;
        if ($total < 0) {
            $total = 0;
        }
        return view(
            'checkout.index',
            compact(
                'checkoutItems',
                'addresses',
                'subtotal',
                'shippingFee',
                'discount',
                'total',
                'vat',
                'oldInfo'
            )
        );
    }

    /**
     * =====================================================
     * SAVE CHECKOUT INFORMATION
     * =====================================================
     */
    public function saveInformation(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'phone' => 'required',
            'delivery_type' => 'required',
        ]);

        $province = null;
        $district = null;
        $ward = null;
        $streetAddress = null;

        if ($request->delivery_type == 'home') {
            if ($request->address_type == 'saved') {
                $address = ShippingAddress::where('address_id', $request->shipping_address_id)
                    ->where('user_id', Auth::id())
                    ->first();

                if (!$address) {
                    return back()->with(
                        'error',
                        'Vui lòng chọn địa chỉ'
                    );
                }

                $province = $address->province;
                $district = $address->district;
                $ward = $address->ward;
                $streetAddress = $address->street_address;
            } else {
                $request->validate([
                    'province' => 'required',
                    'district' => 'required',
                    'ward' => 'required',
                    'street_address' => 'required',
                ]);

                $province = $request->province;
                $district = $request->district;
                $ward = $request->ward;
                $streetAddress = $request->street_address;
            }
        }

        session([
            'checkout_information' => [
                'delivery_type' => $request->delivery_type,
                'address_type' => $request->address_type,
                'shipping_address_id' => $request->shipping_address_id,
                'pickup_store' => $request->pickup_store,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'province' => $province,
                'district' => $district,
                'ward' => $ward,
                'street_address' => $streetAddress,
                'note' => $request->note,
            ],
        ]);

        return redirect()->route('checkout.payment');
    }

    /**
     * =====================================================
     * PAYMENT PAGE STEP 2
     * =====================================================
     */
    public function payment()
    {
        $checkoutItems = session()->get('checkout_items', []);

        if (empty($checkoutItems)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Không có sản phẩm');
        }

        $subtotal = 0;

        foreach ($checkoutItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $info = session('checkout_information', []);

        $province = str_replace(
            ['Tỉnh ', 'Thành phố '],
            '',
            $info['province'] ?? ''
        );

        $shippingFee = $this->getShippingFeeByProvince($province);
        $discount = $this->getDiscountAmount($subtotal);
        $vat = $subtotal * 0.1;

        $total = $subtotal + $shippingFee + $vat - $discount;
        if ($total < 0) {
            $total = 0;
        }

        $addresses = ShippingAddress::where('user_id', Auth::id())->get();

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

    /**
     * =====================================================
     * STORE ORDER
     * =====================================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);

        $info = session('checkout_information');

        if (!$info) {
            return redirect()
                ->route('checkout')
                ->with('error', 'Vui lòng nhập thông tin giao hàng');
        }

        $checkoutItems = session()->get('checkout_items', []);

        if (empty($checkoutItems)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Không có sản phẩm để thanh toán');
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;

            foreach ($checkoutItems as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $shippingFee = $this->getShippingFeeByProvince($info['province']);

            $voucherId = null;
            $discount = $this->getDiscountAmount($subtotal, $voucherId);
            $vat = $subtotal * 0.1;

            $total = $subtotal + $shippingFee + $vat - $discount;
            if ($total < 0) {
                $total = 0;
            }

            $shippingAddressId = null;

            if ($info['delivery_type'] == 'home') {
                if ($info['address_type'] == 'saved') {
                    $shippingAddressId = $info['shipping_address_id'];
                } else {
                    $existAddress = ShippingAddress::where('user_id', Auth::id())
                        ->where('province', $info['province'])
                        ->where('district', $info['district'])
                        ->where('ward', $info['ward'])
                        ->where('street_address', $info['street_address'])
                        ->first();

                    if ($existAddress) {
                        $shippingAddressId = $existAddress->address_id;
                    } else {
                        $address = ShippingAddress::create([
                            'user_id' => Auth::id(),
                            'full_name' => $info['full_name'],
                            'phone' => $info['phone'],
                            'province' => $info['province'],
                            'district' => $info['district'],
                            'ward' => $info['ward'],
                            'street_address' => $info['street_address'],
                            'is_default' => 0,
                        ]);

                        $shippingAddressId = $address->address_id;
                    }
                }
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_address_id' => $shippingAddressId,
                'voucher_id' => $voucherId,
                'order_code' => 'ORD-' . time(),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'cancel_reason' => null,
                'paid_at' => null,
            ]);

            if ($voucherId) {
                DB::table('vouchers')->where('voucher_id', $voucherId)->increment('used_count');
            }

            foreach ($checkoutItems as $item) {
                $variant = ProductVariant::where('variant_id', $item['variant_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$variant) {
                    throw new \Exception('Biến thể sản phẩm không tồn tại');
                }

                if ($variant->stock_quantity < $item['quantity']) {
                    throw new \Exception('Sản phẩm ' . $item['name'] . ' không đủ tồn kho');
                }

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'variant_id' => $variant->variant_id,
                    'product_name' => $item['name'],
                    'variant_info' => is_array($item['variant_name'] ?? null)
                        ? implode(' - ', $item['variant_name'])
                        : ($item['variant_name'] ?? null),
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                $variant->decrement('stock_quantity', $item['quantity']);
            }

            Payment::create([
                'order_id' => $order->order_id,
                'gateway' => $request->payment_method,
                'transaction_id' => strtoupper($request->payment_method) . '-' . time(),
                'amount' => $total,
                'status' => 'pending',
            ]);

            if ($request->payment_method == 'momo') {
                session([
                    'pending_order_id' => $order->order_id,
                ]);

                DB::commit();

                return $this->momoPayment($total);
            }

            if ($request->payment_method === 'vnpay') {
                DB::commit();

                return $this->vnpayPayment($order);
            }

            $selectedCartIds = session()->get('selected_cart_ids', []);

            foreach ($selectedCartIds as $cartKey) {
                $parts = explode('_variant_', $cartKey);
                $variantId = $parts[1] ?? null;

                if (!$variantId) {
                    continue;
                }

                DB::table('cart_items')
                    ->whereIn(
                        'cart_id',
                        Cart::where('user_id', Auth::id())->pluck('cart_id')
                    )
                    ->where('variant_id', $variantId)
                    ->delete();
            }

            $order->update([
                'order_status' => 'processing',
            ]);

            Payment::where('order_id', $order->order_id)
                ->update([
                    'status' => 'success',
                ]);

            // Tự động gửi mail hóa đơn PDF
            $this->sendInvoiceEmail($order->order_id);

            session()->forget([
                'checkout_items',
                'selected_cart_ids',
                'checkout_information',
                'is_reorder',
                'applied_voucher',
            ]);

            $this->syncCartSessionAfterCheckout();

            DB::commit();

            return redirect()
                ->route('orders.history')
                ->with('success_order', [
                    'code' => $order->order_code,
                    'total' => $order->total_amount,
                ]);
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    /**
     * =====================================================
     * CHECKOUT SUCCESS
     * =====================================================
     */
    public function success($id)
    {
        $order = Order::with('items')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('checkout.success', compact('order'));
    }

    /**
     * =====================================================
     * MOMO PAYMENT
     * =====================================================
     */
    public function momoPayment($amount = null)
{
    $checkoutItems = session()->get('checkout_items', []);

    $subtotal = 0;

    foreach ($checkoutItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    $shippingFee = session('shipping_fee', 30000);
    $discount = session('discount_amount', 0);
    $vat = $subtotal * 0.1;

    $total = $subtotal + $shippingFee + $vat - $discount;

    $orderCode = 'MOMO-' . time();

    $phone = 'PSP2615014800000215';

    $qr = "https://img.vietqr.io/image/momo-{$phone}-compact2.png"
        . "?amount=" . (int)$total
        . "&addInfo=" . urlencode($orderCode);

    return view(
        'checkout.momo',
        compact(
            'qr',
            'orderCode',
            'checkoutItems',
            'subtotal',
            'shippingFee',
            'discount',
            'vat',
            'total'
        )
    );
}

    /**
     * =====================================================
     * MOMO RETURN
     * =====================================================
     */
   public function momoReturn(Request $request)
{
    $order = Order::latest('order_id')
        ->where('user_id', Auth::id())
        ->first();

    if (!$order) {
        return redirect('/')
            ->with('error', 'Không tìm thấy đơn hàng');
    }

    if (($request->resultCode ?? 0) == 0) {

        $order->update([
            'payment_status' => 'paid',
            'order_status' => 'processing',
            'paid_at' => now(),
        ]);

        Payment::where('order_id', $order->order_id)
            ->update([
                'status' => 'success',
            ]);

        $this->sendInvoiceEmail($order->order_id);

        return redirect()
            ->route('orders.history')
            ->with('success_order', [
                'code' => $order->order_code,
                'total' => $order->total_amount,
            ]);
    }

    return redirect()
        ->route('checkout.payment')
        ->with('error', 'Thanh toán thất bại');
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
                'Content-Length: ' . strlen($data),
            ],
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            dd(curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }

    /**
     * =====================================================
     * VNPAY PAYMENT
     * =====================================================
     */
    public function vnpayPayment($order)
    {
        $vnp_Returnurl = env('VNP_RETURN_URL');

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
            "vnp_TxnRef" => $order->order_code,
            "vnp_Version" => "2.1.0",
        ];

        return redirect()->route('vnpay.mock_portal', $vnp_Params);
    }

    /**
     * =====================================================
     * VNPAY MOCK PORTAL
     * =====================================================
     */
    public function vnpayMockPortal(Request $request)
    {
        $amount = $request->vnp_Amount / 100;
        $orderCode = $request->vnp_TxnRef;

        $bank = "VCB";
        $accountNo = "1833438176";
        $accountName = "NGUYEN XUAN THU TRANG";

        $amount = (int) $amount;
        $content = urlencode($orderCode);

        $qr =
            "https://img.vietqr.io/image/"
            . $bank
            . "-"
            . $accountNo
            . "-compact2.png"
            . "?amount=" . $amount
            . "&addInfo=" . $content
            . "&accountName=" . urlencode($accountName);

        $checkoutItems = session()->get(
            'checkout_items',
            []
        );

        $subtotal = 0;

        foreach ($checkoutItems as $item) {

            $subtotal +=
                $item['price']
                * $item['quantity'];
        }

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
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view(

            'checkout.vnpay',

            compact(

                'amount',

                'orderCode',

                'qr',

                'request',

                'checkoutItems',

                'subtotal',

                'shippingFee',

                'discount',

                'vat',

                'total'
            )
        );

        return view(
            'checkout.vnpay',
            compact(
                'amount',
                'orderCode',
                'qr',
                'request'
            )
        );

    }

    /**
     * =====================================================
     * VNPAY RETURN
     * =====================================================
     */
    public function vnpayReturn(Request $request)
    {
        $orderCode = $request->vnp_TxnRef;
        $responseCode = $request->vnp_ResponseCode;

        $order = Order::where('order_code', $orderCode)->first();

        if (!$order) {
            return redirect()
                ->route('orders.history')
                ->with('error', 'Không tìm thấy đơn hàng');
        }

        if ($responseCode == '00') {
            $order->update([
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'paid_at' => now(),
            ]);

            Payment::where('order_id', $order->order_id)
                ->update([
                    'status' => 'success',
                ]);

            // Tự động gửi mail hóa đơn PDF
            $this->sendInvoiceEmail($order->order_id);

            foreach ($order->items as $item) {
                DB::table('cart_items')
                    ->whereIn(
                        'cart_id',
                        Cart::where('user_id', $order->user_id)->pluck('cart_id')
                    )
                    ->where('variant_id', $item->variant_id)
                    ->delete();
            }

            session()->forget([
                'checkout_items',
                'selected_cart_ids',
                'checkout_information',
                'is_reorder',
                'pending_order_id',
                'applied_voucher',
            ]);

            $this->syncCartSessionAfterCheckout();

            return redirect()
                ->route('orders.history')
                ->with('success_order', [
                    'code' => $order->order_code,
                    'total' => $order->total_amount,
                ]);
        }

        foreach ($order->items as $item) {
            ProductVariant::where('variant_id', $item->variant_id)
                ->increment('stock_quantity', $item->quantity);
        }

        return redirect()
            ->route('checkout.payment')
            ->with(
                'error',
                'Thanh toán thất bại hoặc bị hủy.'
            );
    }

    /**
     * =====================================================
     * AJAX LẤY PHÍ SHIP
     * =====================================================
     */
    public function getShippingFeeAjax(Request $request)
    {
        $province = $request->province;

        $province = str_replace(
            ['Tỉnh ', 'Thành phố '],
            '',
            trim((string) $province)
        );

        $shipping = ShippingFee::where('province', $province)->first();

        return response()->json([
            'fee' => $shipping->fee ?? 30000,
            'estimated_days' => $shipping->estimated_days ?? 3,
        ]);
    }

    /**
     * =====================================================
     * HÀM LẤY PHÍ SHIP THEO TỈNH
     * =====================================================
     */
    private function getShippingFeeByProvince($province)
    {
        $province = str_replace(
            ['Tỉnh ', 'Thành phố '],
            '',
            trim((string) $province)
        );

        $shipping = ShippingFee::where('province', $province)->first();

        return $shipping ? $shipping->fee : 30000;
    }

    private function getDiscountAmount($subtotal, &$voucherIdOut = null)
    {
        $voucherId = session('applied_voucher');
        $discount = 0;

        if ($voucherId && $subtotal > 0) {
            $voucher = DB::table('vouchers')->where('voucher_id', $voucherId)->lockForUpdate()->first();
            if ($voucher) {
                $now = now();
                $isValid = $voucher->is_active
                    && (!$voucher->end_at || $now->lte($voucher->end_at))
                    && ($voucher->usage_limit === null || $voucher->used_count < $voucher->usage_limit)
                    && ($voucher->min_order_value === null || $subtotal >= $voucher->min_order_value);

                if ($isValid) {
                    if ($voucher->type === 'percent') {
                        $discount = $subtotal * ($voucher->value / 100);
                        if ($voucher->max_discount) {
                            $discount = min($discount, $voucher->max_discount);
                        }
                    } else {
                        $discount = min(max(0, $voucher->value), $subtotal);
                    }
                    $voucherIdOut = $voucher->voucher_id;
                } else {
                    session()->forget('applied_voucher');
                }
            }
        }

        return $discount;
    }

    private function syncCartSessionAfterCheckout()
    {
        if (Auth::check()) {
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
                    $image = DB::table('product_images')
                        ->where('product_id', $product->product_id)
                        ->where('is_primary', 1)
                        ->value('image_url');
                    
                    $cartKey = $product->product_id . '_variant_' . $variant->variant_id;
                    
                    $variantName = null;
                    if (is_array($variant->attribute_values)) {
                        $variantName = implode(' - ', $variant->attribute_values);
                    } elseif (is_string($variant->attribute_values)) {
                        $decoded = json_decode($variant->attribute_values, true);
                        $variantName = is_array($decoded) ? implode(' - ', $decoded) : $variant->attribute_values;
                    }

                    $cart[$cartKey] = [
                        'product_id' => $product->product_id,
                        'variant_id' => $variant->variant_id,
                        'name' => $product->name,
                        'variant_name' => $variantName,
                        'quantity' => (int) $item->quantity,
                        'price' => (float) $item->price,
                        'image' => $image ?? 'images/default-product.png',
                    ];
                }
            }
            session()->put('cart', $cart);

            $totalQuantity = 0;
            foreach ($cart as $it) {
                $totalQuantity += (int) ($it['quantity'] ?? 1);
            }
            session()->put('cart_count', $totalQuantity);
        }
    }

    /**
     * Gửi email kèm hóa đơn PDF khi đặt hàng thành công
     */
    private function sendInvoiceEmail($orderId)
    {
        try {
            $order = DB::table('orders')
                ->leftJoin('users', 'orders.user_id', '=', 'users.user_id')
                ->leftJoin('shipping_addresses', 'orders.shipping_address_id', '=', 'shipping_addresses.address_id')
                ->select(
                    'orders.*',
                    'users.full_name as user_full_name',
                    'users.email as user_email',
                    'shipping_addresses.full_name as receiver_name',
                    'shipping_addresses.phone',
                    'shipping_addresses.province',
                    'shipping_addresses.district',
                    'shipping_addresses.ward',
                    'shipping_addresses.street_address'
                )
                ->where('orders.order_id', $orderId)
                ->first();

            if (!$order || empty($order->user_email)) {
                return;
            }

            $items = DB::table('order_items')
                ->leftJoin('product_variants', 'order_items.variant_id', '=', 'product_variants.variant_id')
                ->leftJoin('products', 'product_variants.product_id', '=', 'products.product_id')
                ->select(
                    'order_items.*',
                    'products.name as product_name_db',
                    'product_variants.sku',
                    'product_variants.attribute_values'
                )
                ->where('order_items.order_id', $orderId)
                ->get();

            \Illuminate\Support\Facades\Mail::to($order->user_email)
                ->send(new \App\Mail\OrderInvoiceMail($order, $items));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gửi mail hóa đơn thất bại cho đơn hàng #' . $orderId . ': ' . $e->getMessage());
        }
    }
}
