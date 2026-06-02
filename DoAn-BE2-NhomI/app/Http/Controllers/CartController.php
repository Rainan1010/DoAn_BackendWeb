<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;

class CartController extends Controller
{
    private function getUserCart()
    {
        return Cart::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'session_id' => null,
                'updated_at' => now(),
            ]
        );
    }

    private function formatVariantName($attributeValues)
    {
        if (is_array($attributeValues)) {
            return implode(' - ', $attributeValues);
        }

        if (is_string($attributeValues)) {
            $decoded = json_decode($attributeValues, true);

            if (is_array($decoded)) {
                return implode(' - ', $decoded);
            }

            return $attributeValues;
        }

        return null;
    }

    private function makeCartKey($productId, $variantId = null)
    {
        return $variantId
            ? $productId . '_variant_' . $variantId
            : (string) $productId;
    }

    private function getVariantIdFromCartKey($cartKey)
    {
        if (preg_match('/_variant_(\d+)$/', $cartKey, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function getDatabaseCartAsSessionFormat()
    {
        $cart = [];

        $userCart = Cart::with(['items.variant.product'])
            ->where('user_id', auth()->id())
            ->first();

        if (!$userCart) {
            return $cart;
        }

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

            $cartKey = $this->makeCartKey($product->product_id, $variant->variant_id);

            $cart[$cartKey] = [
                'product_id' => $product->product_id,
                'variant_id' => $variant->variant_id,
                'name' => $product->name,
                'variant_name' => $this->formatVariantName($variant->attribute_values),
                'quantity' => (int) $item->quantity,
                'price' => (float) $item->price,
                'image' => $image ?? 'images/default-product.png',
            ];
        }

        return $cart;
    }

    private function updateCartCountFromCartArray(array $cart)
    {
        $totalQuantity = 0;

        foreach ($cart as $item) {
            $totalQuantity += (int) ($item['quantity'] ?? 1);
        }

        session()->put('cart_count', $totalQuantity);

        return $totalQuantity;
    }

    private function syncCartSession()
    {
        if (auth()->check()) {
            $cart = $this->getDatabaseCartAsSessionFormat();
            session()->put('cart', $cart);
            $this->updateCartCountFromCartArray($cart);

            return $cart;
        }

        $cart = session()->get('cart', []);
        $this->updateCartCountFromCartArray($cart);

        return $cart;
    }

    private function getCartForCurrentUser()
    {
        return $this->syncCartSession();
    }

    public function index()
    {
        $cart = $this->getCartForCurrentUser();

        $selectedCartIds = session()->get('selected_cart_ids', []);

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

        $discount = 0;
        $appliedVoucher = null;

        $voucherId = session('applied_voucher');

        if ($voucherId && $subtotal > 0) {
            $voucher = DB::table('vouchers')->where('voucher_id', $voucherId)->first();
            $now = now();

            $isValid = $voucher
                && $voucher->is_active
                && (!$voucher->end_at || $now->lte($voucher->end_at))
                && (!($voucher->usage_limit !== null) || $voucher->used_count < $voucher->usage_limit)
                && (!is_numeric($voucher->min_order_value) || $subtotal >= $voucher->min_order_value);

            if ($isValid) {
                if ($voucher->type === 'percent') {
                    $discount = $subtotal * ($voucher->value / 100);

                    if ($voucher->max_discount) {
                        $discount = min($discount, $voucher->max_discount);
                    }
                } else {
                    $discount = min(max(0, $voucher->value), $subtotal);
                }

                $appliedVoucher = $voucher;
            } else {
                session()->forget('applied_voucher');
            }
        }

        $total = $subtotal + $shipping + $tax - $discount;

        return view('cart.cart', compact(
            'cart',
            'selectedCartIds',
            'subtotal',
            'shipping',
            'tax',
            'discount',
            'appliedVoucher',
            'total'
        ));
    }

    public function add(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|integer',
        ]);

        $product = DB::table('products')
            ->where('product_id', $request->id)
            ->first();

        if (!$product) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }

        $quantity = (int) $request->quantity;

        $variant = null;

        if ($request->filled('variant_id')) {
            $variant = ProductVariant::where('variant_id', $request->variant_id)
                ->where('product_id', $request->id)
                ->where('is_active', 1)
                ->first();
        } else {
            $variant = ProductVariant::where('product_id', $request->id)
                ->where('is_active', 1)
                ->orderBy('variant_id', 'asc')
                ->first();
        }

        $image = DB::table('product_images')
            ->where('product_id', $request->id)
            ->where('is_primary', 1)
            ->value('image_url');

        /*
        |--------------------------------------------------------------------------
        | USER ĐÃ ĐĂNG NHẬP: LƯU DATABASE + ĐỒNG BỘ SESSION
        |--------------------------------------------------------------------------
        */
        if (auth()->check()) {
            if (!$variant) {
                return redirect()
                    ->back()
                    ->with('error', 'Sản phẩm này chưa có biến thể nên chưa thể lưu vào giỏ hàng!');
            }

            $price = $variant->sale_price ?? $variant->price ?? $product->base_price;

            $userCart = $this->getUserCart();

            $cartItem = CartItem::where('cart_id', $userCart->cart_id)
                ->where('variant_id', $variant->variant_id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->price = $price;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $userCart->cart_id,
                    'variant_id' => $variant->variant_id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            }

            $userCart->update([
                'updated_at' => now(),
            ]);

            $cartKey = $this->makeCartKey($product->product_id, $variant->variant_id);

            $selectedCartIds = session()->get('selected_cart_ids', []);

            if (!in_array($cartKey, $selectedCartIds)) {
                $selectedCartIds[] = $cartKey;
            }

            session()->put('selected_cart_ids', $selectedCartIds);

            // Quan trọng: đồng bộ database cart sang session để navbar trang chủ đọc được
            $this->syncCartSession();

            return redirect()
                ->route('cart.index')
                ->with('success', 'Đã thêm vào giỏ hàng!');
        }

        /*
        |--------------------------------------------------------------------------
        | KHÁCH CHƯA ĐĂNG NHẬP: LƯU SESSION
        |--------------------------------------------------------------------------
        */
        $cart = session()->get('cart', []);

        $cartKey = $this->makeCartKey($request->id, $variant->variant_id ?? null);

        $price = $variant
            ? ($variant->sale_price ?? $variant->price)
            : $product->base_price;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->product_id,
                'variant_id' => $variant->variant_id ?? null,
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $price,
                'image' => $image ?? 'images/default-product.png',
            ];

            if ($variant) {
                $cart[$cartKey]['variant_name'] = $this->formatVariantName($variant->attribute_values);
            }
        }

        session()->put('cart', $cart);
        $this->updateCartCountFromCartArray($cart);

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

        $cart = $this->getCartForCurrentUser();
        $selectedCartIds = session()->get('selected_cart_ids', []);

        if (!isset($cart[$request->id])) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Sản phẩm không tồn tại trong giỏ hàng!');
        }

        if (in_array($request->id, $selectedCartIds)) {
            $selectedCartIds = array_values(array_filter($selectedCartIds, function ($id) use ($request) {
                return $id != $request->id;
            }));
        } else {
            $selectedCartIds[] = $request->id;
        }

        session()->put('selected_cart_ids', $selectedCartIds);

        return redirect()->route('cart.index');
    }

    public function select(Request $request)
    {
        $cart = $this->getCartForCurrentUser();

        if ($request->input('action') === 'clear') {
            session()->put('selected_cart_ids', []);
        } else {
            session()->put('selected_cart_ids', array_keys($cart));
        }

        return redirect()->route('cart.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        if (auth()->check()) {
            $variantId = $this->getVariantIdFromCartKey($request->id);

            if (!$variantId) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Không xác định được biến thể sản phẩm!');
            }

            $userCart = Cart::where('user_id', auth()->id())->first();

            if (!$userCart) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Giỏ hàng không tồn tại!');
            }

            $cartItem = CartItem::where('cart_id', $userCart->cart_id)
                ->where('variant_id', $variantId)
                ->first();

            if (!$cartItem) {
                return redirect()
                    ->route('cart.index')
                    ->with('error', 'Sản phẩm không tồn tại trong giỏ hàng!');
            }

            $cartItem->update([
                'quantity' => (int) $request->quantity,
            ]);

            $userCart->update([
                'updated_at' => now(),
            ]);

            // Đồng bộ lại session để navbar trang chủ cũng hiện đúng số
            $this->syncCartSession();

            return redirect()
                ->route('cart.index')
                ->with('success', 'Giỏ hàng đã được cập nhật!');
        }

        $cart = session()->get('cart', []);

        if (!isset($cart[$request->id])) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Sản phẩm không tồn tại trong giỏ hàng!');
        }

        $cart[$request->id]['quantity'] = (int) $request->quantity;

        session()->put('cart', $cart);
        $this->updateCartCountFromCartArray($cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Giỏ hàng đã được cập nhật!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        if (auth()->check()) {
            $variantId = $this->getVariantIdFromCartKey($request->id);

            if ($variantId) {
                $userCart = Cart::where('user_id', auth()->id())->first();

                if ($userCart) {
                    CartItem::where('cart_id', $userCart->cart_id)
                        ->where('variant_id', $variantId)
                        ->delete();

                    $userCart->update([
                        'updated_at' => now(),
                    ]);
                }
            }

            // Đồng bộ lại session sau khi xóa
            $this->syncCartSession();
        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
                $this->updateCartCountFromCartArray($cart);
            }
        }

        $selectedCartIds = session()->get('selected_cart_ids', []);

        $selectedCartIds = array_values(array_filter($selectedCartIds, function ($id) use ($request) {
            return $id != $request->id;
        }));

        session()->put('selected_cart_ids', $selectedCartIds);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Sản phẩm đã được xoá khỏi giỏ hàng!');
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->voucher_code));

        $voucher = DB::table('vouchers')->where('code', $code)->first();

        if (!$voucher) {
            return redirect()->route('cart.index')
                ->with('voucher_error', 'Mã giảm giá không tồn tại.');
        }

        if (!$voucher->is_active) {
            return redirect()->route('cart.index')
                ->with('voucher_error', 'Mã giảm giá đã bị vô hiệu hoá.');
        }

        $now = now();

        if ($voucher->start_at && $now->lt($voucher->start_at)) {
            return redirect()->route('cart.index')
                ->with('voucher_error', 'Mã giảm giá chưa đến thời gian sử dụng.');
        }

        if ($voucher->end_at && $now->gt($voucher->end_at)) {
            return redirect()->route('cart.index')
                ->with('voucher_error', 'Mã giảm giá đã hết hạn.');
        }

        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            return redirect()->route('cart.index')
                ->with('voucher_error', 'Mã giảm giá đã được sử dụng hết lượt.');
        }

        $cart = $this->getCartForCurrentUser();
        $selectedCartIds = session()->get('selected_cart_ids', []);
        $subtotal = 0;

        foreach ($selectedCartIds as $id) {
            if (isset($cart[$id])) {
                $subtotal += $cart[$id]['price'] * $cart[$id]['quantity'];
            }
        }

        if ($subtotal <= 0) {
            return redirect()->route('cart.index')
                ->with('voucher_error', 'Vui lòng chọn ít nhất một sản phẩm trước khi áp dụng mã giảm giá.');
        }

        if (is_numeric($voucher->min_order_value) && $subtotal < $voucher->min_order_value) {
            return redirect()->route('cart.index')
                ->with('voucher_error', 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher->min_order_value, 0, ',', '.') . '₫ để dùng mã này.');
        }

        session()->put('applied_voucher', $voucher->voucher_id);

        return redirect()->route('cart.index')
            ->with('voucher_success', 'Áp dụng mã "' . $code . '" thành công!');
    }

    public function removeVoucher()
    {
        session()->forget('applied_voucher');

        return redirect()->route('cart.index')
            ->with('voucher_success', 'Đã xoá mã giảm giá.');
    }
}
