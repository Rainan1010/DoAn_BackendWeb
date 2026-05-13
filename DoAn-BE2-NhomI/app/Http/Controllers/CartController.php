<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $selectedCartIds = session()->get('selected_cart_ids', []);

        // Nếu sản phẩm đã bị xoá khỏi giỏ thì loại khỏi danh sách đang chọn
        $selectedCartIds = array_values(array_filter($selectedCartIds, function ($id) use ($cart) {
            return isset($cart[$id]);
        }));

        session()->put('selected_cart_ids', $selectedCartIds);

        $subtotal = 0;

        foreach ($selectedCartIds as $id) {
            if (isset($cart[$id])) {
                $subtotal += $cart[$id]['price'] * $cart[$id]['quantity'];
            }
        }

        $shipping = $subtotal > 0 ? 45000 : 0;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $shipping + $tax;

        return view('cart.cart', compact(
            'cart',
            'selectedCartIds',
            'subtotal',
            'shipping',
            'tax',
            'total'
        ));
    }

   public function add(Request $request)
{
    $request->validate([
        'id' => 'required',
        'quantity' => 'required|integer|min:1',
    ]);

    $product = DB::table('products')
        ->where('product_id', $request->id)
        ->first();

    if (!$product) {
        return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
    }

    $quantity = (int) $request->quantity;

    $cart = session()->get('cart', []);

    /*
    |--------------------------------------------------------------------------
    | Nếu có variant thì lấy thông tin variant
    |--------------------------------------------------------------------------
    */
    $variant = null;

    if ($request->filled('variant_id')) {
        $variant = DB::table('product_variants')
            ->where('variant_id', $request->variant_id)
            ->where('product_id', $request->id)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Tạo key riêng cho từng sản phẩm / biến thể
    | Ví dụ:
    | product_5
    | product_5_variant_12
    |--------------------------------------------------------------------------
    */
    $cartKey = $variant
        ? $request->id . '_variant_' . $variant->variant_id
        : (string) $request->id;

    $image = DB::table('product_images')
        ->where('product_id', $request->id)
        ->where('is_primary', 1)
        ->value('image_url');

    $price = $variant ? $variant->price : $product->base_price;

    if (isset($cart[$cartKey])) {
        /*
        |--------------------------------------------------------------------------
        | Nếu sản phẩm đã có trong giỏ thì cộng thêm đúng số lượng user chọn
        |--------------------------------------------------------------------------
        */
        $cart[$cartKey]['quantity'] += $quantity;
    } else {
        /*
        |--------------------------------------------------------------------------
        | Nếu chưa có thì thêm mới với đúng số lượng user chọn
        |--------------------------------------------------------------------------
        */
        $cart[$cartKey] = [
            'product_id' => $product->product_id,
            'variant_id' => $variant->variant_id ?? null,
            'name' => $product->name,
            'quantity' => $quantity,
            'price' => $price,
            'image' => $image ?? 'images/default-product.png',
        ];

        if ($variant) {
            $cart[$cartKey]['variant_name'] = $variant->attribute_values;
        }
    }

    session()->put('cart', $cart);

    /*
    |--------------------------------------------------------------------------
    | Nếu bạn đang dùng chọn nhiều sản phẩm trong giỏ hàng
    | thì tự động chọn sản phẩm vừa thêm
    |--------------------------------------------------------------------------
    */
    $selectedCartIds = session()->get('selected_cart_ids', []);

    if (!in_array($cartKey, $selectedCartIds)) {
        $selectedCartIds[] = $cartKey;
    }

    session()->put('selected_cart_ids', $selectedCartIds);

    return redirect()
        ->route('cart.index')
        ->with('success', 'Đã thêm vào giỏ hàng!');
}

    public function toggleSelect(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $cart = session()->get('cart', []);
        $selectedCartIds = session()->get('selected_cart_ids', []);

        if (!isset($cart[$request->id])) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Sản phẩm không tồn tại trong giỏ hàng!');
        }

        if (in_array($request->id, $selectedCartIds)) {
            // Nếu đang chọn rồi thì bỏ chọn
            $selectedCartIds = array_values(array_filter($selectedCartIds, function ($id) use ($request) {
                return $id != $request->id;
            }));
        } else {
            // Nếu chưa chọn thì thêm vào danh sách chọn
            $selectedCartIds[] = $request->id;
        }

        session()->put('selected_cart_ids', $selectedCartIds);

        return redirect()->route('cart.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (!isset($cart[$request->id])) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Sản phẩm không tồn tại trong giỏ hàng!');
        }

        $cart[$request->id]['quantity'] = (int) $request->quantity;

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Giỏ hàng đã được cập nhật!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }

        // Nếu sản phẩm bị xoá đang nằm trong danh sách được chọn thì xoá khỏi selected_cart_ids
        $selectedCartIds = session()->get('selected_cart_ids', []);

        $selectedCartIds = array_values(array_filter($selectedCartIds, function ($id) use ($request) {
            return $id != $request->id;
        }));

        session()->put('selected_cart_ids', $selectedCartIds);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Sản phẩm đã được xoá khỏi giỏ hàng!');
    }
}