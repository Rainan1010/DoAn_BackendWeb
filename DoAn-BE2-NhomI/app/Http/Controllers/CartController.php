<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = $subtotal > 0 ? 45000 : 0;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $shipping + $tax;

        // Nếu tệp là resources/views/cart.blade.php
        return view('cart.cart', compact('cart', 'subtotal', 'shipping', 'tax', 'total'));
    }

    public function add(Request $request)
    {
        $product = DB::table('products')->where('product_id', $request->id)->first();

        if (!$product) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity']++;
        } else {
            $image = DB::table('product_images')
                ->where('product_id', $request->id)
                ->where('is_primary', 1)
                ->value('image_url');

            $cart[$request->id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->base_price,
                "image" => $image
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }
    // app/Http/Controllers/CartController.php

    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');

            // Cập nhật số lượng mới
            $cart[$request->id]["quantity"] = $request->quantity;
            if (isset($cart[$request->id])) {
                // Cập nhật số lượng mới
                $cart[$request->id]["quantity"] = $request->quantity;
            }

            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Giỏ hàng đã được cập nhật!');
        }
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Sản phẩm đã được xoá khỏi giỏ hàng!');
        }
    }
}
