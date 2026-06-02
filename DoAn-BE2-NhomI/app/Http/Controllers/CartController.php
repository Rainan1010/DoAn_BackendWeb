<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

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

    private function formatVariantName($attributeValues)
    {
        if (empty($attributeValues)) {
            return null;
        }

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

    private function getUserCart()
    {
        $cart = DB::table('carts')
            ->where('user_id', auth()->id())
            ->first();

        if ($cart) {
            DB::table('carts')
                ->where('cart_id', $cart->cart_id)
                ->update([
                    'session_id' => session()->getId(),
                    'updated_at' => now(),
                ]);

            return DB::table('carts')
                ->where('cart_id', $cart->cart_id)
                ->first();
        }

        $cartId = DB::table('carts')->insertGetId([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'updated_at' => now(),
        ]);

        return DB::table('carts')
            ->where('cart_id', $cartId)
            ->first();
    }

    private function getDatabaseCartAsSessionFormat()
    {
        $cart = [];

        if (!auth()->check()) {
            return $cart;
        }

        $userCart = DB::table('carts')
            ->where('user_id', auth()->id())
            ->first();

        if (!$userCart) {
            return $cart;
        }

        $items = DB::table('cart_items')
            ->join('product_variants', 'cart_items.variant_id', '=', 'product_variants.variant_id')
            ->join('products', 'product_variants.product_id', '=', 'products.product_id')
            ->leftJoin('product_images', function ($join) {
                $join->on('products.product_id', '=', 'product_images.product_id')
                    ->where('product_images.is_primary', 1);
            })
            ->where('cart_items.cart_id', $userCart->cart_id)
            ->select(
                'cart_items.item_id',
                'cart_items.variant_id',
                'cart_items.quantity',
                'cart_items.price',
                'product_variants.product_id',
                'product_variants.attribute_values',
                'product_variants.stock_quantity',
                'products.name',
                'product_images.image_url'
            )
            ->get();

        foreach ($items as $item) {
            $cartKey = $this->makeCartKey($item->product_id, $item->variant_id);

            $cart[$cartKey] = [
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'name' => $item->name,
                'variant_name' => $this->formatVariantName($item->attribute_values),
                'quantity' => (int) $item->quantity,
                'price' => (float) $item->price,
                'stock_quantity' => (int) $item->stock_quantity,
                'image' => $item->image_url ?? 'images/default-product.png',
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

    public function syncCartSession()
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

    private function getProductImage($productId)
    {
        return DB::table('product_images')
            ->where('product_id', $productId)
            ->where('is_primary', 1)
            ->value('image_url') ?? 'images/default-product.png';
    }

    private function getDefaultVariant($productId, $variantId = null)
    {
        $query = DB::table('product_variants')
            ->where('product_id', $productId)
            ->where('is_active', 1);

        if ($variantId) {
            $query->where('variant_id', $variantId);
        }

        return $query->orderBy('variant_id', 'asc')->first();
    }

    /*
    |--------------------------------------------------------------------------
    | CART PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $cart = $this->syncCartSession();

        $selectedCartIds = session()->get('selected_cart_ids', []);

        $selectedCartIds = array_values(array_filter($selectedCartIds, function ($id) use ($cart) {
            return isset($cart[$id]);
        }));

        // Nếu có sản phẩm nhưng chưa chọn sản phẩm nào thì tự chọn tất cả.
        // Tránh lỗi đăng nhập lại có hàng nhưng subtotal = 0.
        if (empty($selectedCartIds) && !empty($cart)) {
            $selectedCartIds = array_keys($cart);
        }

        session()->put('selected_cart_ids', $selectedCartIds);

        $subtotal = 0;

        foreach ($selectedCartIds as $id) {
            if (isset($cart[$id])) {
                $subtotal += (float) $cart[$id]['price'] * (int) $cart[$id]['quantity'];
            }
        }

        $shipping = $subtotal > 0 ? 45000 : 0;
        $tax = $subtotal * 0.1;

        $discount = 0;
        $appliedVoucher = null;

        $voucherId = session('applied_voucher');

        if ($voucherId && $subtotal > 0) {
            $voucher = DB::table('vouchers')
                ->where('voucher_id', $voucherId)
                ->first();

            $now = now();

            $isValid = $voucher
                && (int) $voucher->is_active === 1
                && (!$voucher->end_at || $now->lte($voucher->end_at))
                && ($voucher->usage_limit === null || $voucher->used_count < $voucher->usage_limit)
                && (!is_numeric($voucher->min_order_value) || $subtotal >= $voucher->min_order_value);

            if ($isValid) {
                if ($voucher->type === 'percent') {
                    $discount = $subtotal * ((float) $voucher->value / 100);

                    if ($voucher->max_discount) {
                        $discount = min($discount, (float) $voucher->max_discount);
                    }
                } else {
                    $discount = min(max(0, (float) $voucher->value), $subtotal);
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

    /*
    |--------------------------------------------------------------------------
    | ADD TO CART
    |--------------------------------------------------------------------------
    */

    public function add(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|integer',
        ]);

        $product = DB::table('products')
            ->where('product_id', $request->id)
            ->first();

        if (!$product) {
            return redirect()
                ->back()
                ->with('error', 'Sản phẩm không tồn tại!');
        }

        $quantity = (int) $request->input('quantity', 1);

        $variant = $this->getDefaultVariant($product->product_id, $request->variant_id);

        if (!$variant) {
            return redirect()
                ->back()
                ->with('error', 'Sản phẩm này chưa có dữ liệu tồn kho nên chưa thể thêm vào giỏ hàng!');
        }

        $cartKey = $this->makeCartKey($product->product_id, $variant->variant_id);
        $price = $variant->sale_price ?? $variant->price ?? $product->base_price;
        $image = $this->getProductImage($product->product_id);

        /*
        |--------------------------------------------------------------------------
        | USER ĐÃ ĐĂNG NHẬP: LƯU DATABASE
        |--------------------------------------------------------------------------
        */
        if (auth()->check()) {
            $userCart = $this->getUserCart();

            $existingItem = DB::table('cart_items')
                ->where('cart_id', $userCart->cart_id)
                ->where('variant_id', $variant->variant_id)
                ->first();

            $currentQuantity = $existingItem ? (int) $existingItem->quantity : 0;
            $newQuantity = $currentQuantity + $quantity;

            if ($newQuantity > (int) $variant->stock_quantity) {
                return redirect()
                    ->back()
                    ->with('error', 'Số lượng sản phẩm trong giỏ vượt quá tồn kho hiện có!');
            }

            if ($existingItem) {
                DB::table('cart_items')
                    ->where('item_id', $existingItem->item_id)
                    ->update([
                        'quantity' => $newQuantity,
                        'price' => $price,
                    ]);
            } else {
                DB::table('cart_items')->insert([
                    'cart_id' => $userCart->cart_id,
                    'variant_id' => $variant->variant_id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            }

            $cart = $this->syncCartSession();

            $selectedCartIds = session()->get('selected_cart_ids', []);

            if (!in_array($cartKey, $selectedCartIds)) {
                $selectedCartIds[] = $cartKey;
                session()->put('selected_cart_ids', $selectedCartIds);
            }

            return redirect()
                ->route('cart.index')
                ->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
        }

        /*
        |--------------------------------------------------------------------------
        | KHÁCH CHƯA ĐĂNG NHẬP: LƯU SESSION
        |--------------------------------------------------------------------------
        */
        $cart = session()->get('cart', []);

        $currentQuantity = isset($cart[$cartKey])
            ? (int) $cart[$cartKey]['quantity']
            : 0;

        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > (int) $variant->stock_quantity) {
            return redirect()
                ->back()
                ->with('error', 'Số lượng sản phẩm trong giỏ vượt quá tồn kho hiện có!');
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $newQuantity;
            $cart[$cartKey]['price'] = $price;
            $cart[$cartKey]['stock_quantity'] = (int) $variant->stock_quantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->product_id,
                'variant_id' => $variant->variant_id,
                'name' => $product->name,
                'variant_name' => $this->formatVariantName($variant->attribute_values),
                'quantity' => $quantity,
                'price' => (float) $price,
                'stock_quantity' => (int) $variant->stock_quantity,
                'image' => $image,
            ];
        }

        session()->put('cart', $cart);

        $selectedCartIds = session()->get('selected_cart_ids', []);

        if (!in_array($cartKey, $selectedCartIds)) {
            $selectedCartIds[] = $cartKey;
            session()->put('selected_cart_ids', $selectedCartIds);
        }

        $this->updateCartCountFromCartArray($cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE QUANTITY
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartKey = $request->id;
        $quantity = (int) $request->quantity;
        $variantId = $this->getVariantIdFromCartKey($cartKey);

        if (!$variantId) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Dữ liệu giỏ hàng không hợp lệ!');
        }

        $variant = DB::table('product_variants')
            ->where('variant_id', $variantId)
            ->first();

        if (!$variant) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Sản phẩm trong giỏ không tồn tại!');
        }

        if ($quantity > (int) $variant->stock_quantity) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Số lượng cập nhật vượt quá tồn kho hiện có!');
        }

        if (auth()->check()) {
            $userCart = $this->getUserCart();

            DB::table('cart_items')
                ->where('cart_id', $userCart->cart_id)
                ->where('variant_id', $variantId)
                ->update([
                    'quantity' => $quantity,
                ]);

            $this->syncCartSession();
        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] = $quantity;
                $cart[$cartKey]['stock_quantity'] = (int) $variant->stock_quantity;
                session()->put('cart', $cart);
                $this->updateCartCountFromCartArray($cart);
            }
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã cập nhật số lượng sản phẩm!');
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVE ITEM
    |--------------------------------------------------------------------------
    */

    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $cartKey = $request->id;
        $variantId = $this->getVariantIdFromCartKey($cartKey);

        if (auth()->check() && $variantId) {
            $userCart = $this->getUserCart();

            DB::table('cart_items')
                ->where('cart_id', $userCart->cart_id)
                ->where('variant_id', $variantId)
                ->delete();

            $cart = $this->syncCartSession();
        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$cartKey])) {
                unset($cart[$cartKey]);
            }

            session()->put('cart', $cart);
            $this->updateCartCountFromCartArray($cart);
        }

        $selectedCartIds = session()->get('selected_cart_ids', []);
        $selectedCartIds = array_values(array_filter($selectedCartIds, function ($id) use ($cartKey) {
            return $id !== $cartKey;
        }));

        session()->put('selected_cart_ids', $selectedCartIds);

        if (empty($cart)) {
            session()->forget('applied_voucher');
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT CART ITEMS
    |--------------------------------------------------------------------------
    */

    public function select(Request $request)
    {
        $cart = $this->syncCartSession();

        $selectedCartIds = $request->input('selected_cart_ids', []);

        $selectedCartIds = array_values(array_filter($selectedCartIds, function ($id) use ($cart) {
            return isset($cart[$id]);
        }));

        session()->put('selected_cart_ids', $selectedCartIds);

        return redirect()->route('cart.index');
    }

    public function toggleSelect(Request $request)
    {
        $cart = $this->syncCartSession();

        $cartKey = $request->input('id');

        if (!$cartKey || !isset($cart[$cartKey])) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Sản phẩm trong giỏ không tồn tại!');
        }

        $selectedCartIds = session()->get('selected_cart_ids', []);

        if (in_array($cartKey, $selectedCartIds)) {
            $selectedCartIds = array_values(array_filter($selectedCartIds, function ($id) use ($cartKey) {
                return $id !== $cartKey;
            }));
        } else {
            $selectedCartIds[] = $cartKey;
        }

        session()->put('selected_cart_ids', $selectedCartIds);

        return redirect()->route('cart.index');
    }

    /*
    |--------------------------------------------------------------------------
    | VOUCHER
    |--------------------------------------------------------------------------
    */

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string|max:50',
        ]);

        $cart = $this->syncCartSession();
        $selectedCartIds = session()->get('selected_cart_ids', []);

        $subtotal = 0;

        foreach ($selectedCartIds as $id) {
            if (isset($cart[$id])) {
                $subtotal += (float) $cart[$id]['price'] * (int) $cart[$id]['quantity'];
            }
        }

        if ($subtotal <= 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Vui lòng chọn sản phẩm trước khi áp dụng mã giảm giá!');
        }

        $voucher = DB::table('vouchers')
            ->where('code', strtoupper($request->voucher_code))
            ->orWhere('code', $request->voucher_code)
            ->first();

        if (!$voucher) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Mã giảm giá không tồn tại!');
        }

        $now = now();

        if (!(int) $voucher->is_active) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Mã giảm giá chưa được kích hoạt!');
        }

        if ($voucher->start_at && $now->lt($voucher->start_at)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Mã giảm giá chưa đến thời gian sử dụng!');
        }

        if ($voucher->end_at && $now->gt($voucher->end_at)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Mã giảm giá đã hết hạn!');
        }

        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Mã giảm giá đã hết lượt sử dụng!');
        }

        if (is_numeric($voucher->min_order_value) && $subtotal < $voucher->min_order_value) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã giảm giá!');
        }

        session()->put('applied_voucher', $voucher->voucher_id);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Áp dụng mã giảm giá thành công!');
    }

    public function removeVoucher()
    {
        session()->forget('applied_voucher');

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã xóa mã giảm giá!');
    }
}