<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống');
        }

        $addresses = ShippingAddress::where('user_id', Auth::id())
            ->get();

        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shippingFee = 30000;

        $discount = 0;

        $total = $subtotal + $shippingFee - $discount;

        return view('checkout.index', compact(
            'cart',
            'addresses',
            'subtotal',
            'shippingFee',
            'discount',
            'total'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required',
            'payment_method' => 'required'
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Giỏ hàng trống');
        }

        DB::beginTransaction();

        try {

            $subtotal = 0;

            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $shippingFee = 30000;
            $discount = 0;

            $total = $subtotal + $shippingFee - $discount;

            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_address_id' => $request->shipping_address_id,
                'voucher_id' => null,
                'order_code' => 'BT-' . time(),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            foreach ($cart as $item) {

                $variant = ProductVariant::find($item['variant_id']);

                if (!$variant) {
                    throw new \Exception('Biến thể không tồn tại');
                }

                if ($variant->stock_quantity < $item['quantity']) {
                    throw new \Exception('Sản phẩm không đủ số lượng');
                }

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'variant_id' => $variant->variant_id,
                    'product_name' => $item['name'],
                    'variant_info' => json_encode($item['attributes']),
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                $variant->decrement('stock_quantity', $item['quantity']);
            }

            Payment::create([
                'order_id' => $order->order_id,
                'gateway' => $request->payment_method,
                'transaction_id' => 'TRANS-' . time(),
                'amount' => $total,
                'status' => 'pending',
            ]);

            session()->forget('cart');

            DB::commit();

            return redirect()->route('checkout.success', $order->order_id);

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function success($id)
    {
        $order = Order::with('items')->findOrFail($id);

        return view('checkout.success', compact('order'));
    }
}