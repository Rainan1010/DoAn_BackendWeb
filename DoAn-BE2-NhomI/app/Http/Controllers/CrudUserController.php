<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyOTPMail;
use App\Models\LoginHistory;
class CrudUserController extends Controller
{
    // =====================================================
    // 1. ĐĂNG NHẬP (LOGIN)
    // =====================================================
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            LoginHistory::create([
                'user_id' => $user->user_id,
                'email' => $user->email,
                'login_time' => now(),
                'ip_address' => $request->ip(),
                'status' => 'success',
            ]);
            // Kiểm tra tài khoản bị khóa
            if ($user->is_active == 0) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with('error', 'Tài khoản của bạn đã bị khóa.');
            }

            // Kiểm tra xác thực OTP
            if ($user->is_verified == 0) {
                $userId = $user->user_id;

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                session(['otp_user_id' => $userId]);

                $otpCode = rand(100000, 999999);

                DB::table('otp_verifications')->updateOrInsert(
                    [
                        'user_id' => $userId,
                        'purpose' => 'register',
                    ],
                    [
                        'otp_code' => $otpCode,
                        'expires_at' => now()->addMinutes(10),
                        'used' => 0,
                    ]
                );

                Mail::to($user->email)->send(new VerifyOTPMail($otpCode));

                return redirect()
                    ->route('otp.view')
                    ->with('error', 'Tài khoản chưa xác thực. Mã OTP mới đã được gửi qua email.');
            }

            // Chuyển giỏ hàng session sang database sau khi đăng nhập thành công
            $this->mergeSessionCartToDatabase();

            // Load database cart into session immediately so user has cart in session after login
            (new \App\Http\Controllers\CartController())->syncCartSession();

            // Chuyển hướng theo vai trò
            if ($user->role === 'admin') {
                return redirect('/admin/categories')
                    ->with('success', 'Chào mừng Admin quay trở lại!');
            }

            return redirect('/')
                ->with('success', 'Đăng nhập thành công!');
        }
        LoginHistory::create([
            'user_id' => null,
            'email' => $request->email,
            'login_time' => now(),
            'ip_address' => $request->ip(),
            'status' => 'failed',
        ]);

        return back()
            ->with('error', 'Tài khoản hoặc mật khẩu không chính xác.')
            ->withInput();
    }

    /**
     * Chuyển giỏ hàng lưu trong session sang database sau khi user đăng nhập.
     *
     * Lưu ý:
     * - cart_items của bạn chỉ lưu: cart_id, variant_id, quantity, price
     * - Nếu session cart thiếu variant_id, hàm sẽ tự lấy variant đầu tiên theo product_id
     */
    private function mergeSessionCartToDatabase()
    {
        if (!auth()->check()) {
            return;
        }

        $sessionCart = session()->get('cart', []);

        if (empty($sessionCart)) {
            return;
        }

        $userCart = Cart::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'session_id' => null,
                'updated_at' => now(),
            ]
        );

        $mergedCount = 0;

        foreach ($sessionCart as $cartKey => $details) {
            $variantId = $details['variant_id'] ?? null;

            /*
        |--------------------------------------------------------------------------
        | Trường hợp session cart không có variant_id
        | Thử lấy variant_id từ product_id
        |--------------------------------------------------------------------------
        */
            if (empty($variantId) && !empty($details['product_id'])) {
                $variantId = DB::table('product_variants')
                    ->where('product_id', $details['product_id'])
                    ->where('is_active', 1)
                    ->orderBy('variant_id', 'asc')
                    ->value('variant_id');
            }

            /*
        |--------------------------------------------------------------------------
        | Nếu vẫn không có variant_id thì bỏ qua item này,
        | nhưng KHÔNG xoá session cart để tránh mất giỏ hàng.
        |--------------------------------------------------------------------------
        */
            if (empty($variantId)) {
                continue;
            }

            $variant = DB::table('product_variants')
                ->where('variant_id', $variantId)
                ->first();

            if (!$variant) {
                continue;
            }

            $quantity = max(1, (int) ($details['quantity'] ?? 1));

            $price = $details['price'] ?? null;

            if (empty($price)) {
                $price = $variant->sale_price ?? $variant->price ?? 0;
            }

            $cartItem = CartItem::where('cart_id', $userCart->cart_id)
                ->where('variant_id', $variantId)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->price = $price;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $userCart->cart_id,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
            }

            $mergedCount++;
        }

        $userCart->update([
            'updated_at' => now(),
        ]);

        /*
    |--------------------------------------------------------------------------
    | Chỉ xoá session cart nếu đã merge được ít nhất 1 sản phẩm
    |--------------------------------------------------------------------------
    */
        if ($mergedCount > 0) {
            session()->forget('cart');
        }
    }

    // =====================================================
    // 2. ĐĂNG KÝ (REGISTER)
    // =====================================================
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|string|digits:10',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'email.unique' => 'Email này đã được đăng ký.',
            'phone.digits' => 'Số điện thoại phải gồm đúng 10 số.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => 1,
            'is_verified' => 0,
        ]);

        $otpCode = rand(100000, 999999);

        DB::table('otp_verifications')->insert([
            'user_id' => $user->user_id,
            'otp_code' => $otpCode,
            'purpose' => 'register',
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new VerifyOTPMail($otpCode));

        session(['otp_user_id' => $user->user_id]);

        return redirect()
            ->route('otp.view')
            ->with('success', 'Đăng ký thành công! Vui lòng nhập mã OTP gửi tới email.');
    }

    // =====================================================
    // 3. QUẢN LÝ THÔNG TIN (PROFILE & AVATAR)
    // =====================================================
    public function profile()
    {
        return view('auth.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => ['required', 'max:50', 'regex:/^[\pL\s]+$/u'],
            'phone' => ['required', 'digits:10', 'regex:/^0[0-9]{9}$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'full_name.required' => 'Vui lòng nhập Họ và tên.',
            'phone.digits' => 'Số điện thoại phải gồm đúng 10 số.',
            'avatar.image' => 'File tải lên phải là hình ảnh.',
            'avatar.max' => 'Dung lượng ảnh tối đa là 2MB.',
        ]);

        $user->full_name = trim($request->full_name);
        $user->phone = $request->phone;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = public_path('images/users');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            if ($user->avatar_url && file_exists(public_path($user->avatar_url))) {
                unlink(public_path($user->avatar_url));
            }

            $file->move($path, $fileName);

            $user->avatar_url = 'images/users/' . $fileName;
        }

        $user->save();

        return back()->with('success', 'Cập nhật thông tin tài khoản thành công.');
    }

    // =====================================================
    // 4. ĐỔI MẬT KHẨU (CHANGE PASSWORD)
    // =====================================================
    public function showChangePassword()
    {
        return view('auth.change_password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.min' => 'Mật khẩu mới tối thiểu 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors([
                'current_password' => 'Mật khẩu hiện tại không chính xác.',
            ]);
        }

        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        Auth::logout();

        return redirect('/login')
            ->with('success', 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
    }
    // =====================================================
    // HIỂN THỊ LOGIN HISTORY
    // =====================================================

    public function loginHistory()
    {
        $query = LoginHistory::with('user');

        // FILTER STATUS
        if (request('status')) {
            $query->where('status', request('status'));
        }

        $logs = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.login_history.index',
            compact('logs')
        );
    }
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Capture login history logout_time if available
        if ($user) {
            $history = LoginHistory::where(
                'user_id',
                $user->user_id
            )
                ->whereNull('logout_time')
                ->latest()
                ->first();

            if ($history) {
                $history->update([
                    'logout_time' => now(),
                ]);
            }
        }

        // Preserve cart/session data before invalidating session
        $cart = session()->get('cart', []);
        $cartCount = session()->get('cart_count', 0);
        $selectedCartIds = session()->get('selected_cart_ids', []);
        $appliedVoucher = session()->get('applied_voucher', null);

        // If session cart empty but user has DB cart, load it to restore for guest session
        if (empty($cart) && $user) {
            $userCart = DB::table('carts')->where('user_id', $user->user_id)->first();

            if ($userCart) {
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

                $cart = [];
                $cartCount = 0;
                $selectedCartIds = [];

                foreach ($items as $item) {
                    $cartKey = $item->product_id . '_variant_' . $item->variant_id;

                    $cart[$cartKey] = [
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'name' => $item->name,
                        'variant_name' => is_string($item->attribute_values) ? (is_array(json_decode($item->attribute_values, true)) ? implode(' - ', json_decode($item->attribute_values, true)) : $item->attribute_values) : $item->attribute_values,
                        'quantity' => (int) $item->quantity,
                        'price' => (float) $item->price,
                        'stock_quantity' => (int) $item->stock_quantity,
                        'image' => $item->image_url ?? 'images/default-product.png',
                    ];

                    $cartCount += (int) $item->quantity;
                    $selectedCartIds[] = $cartKey;
                }
            }
        }

        // Perform logout and invalidate session
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Restore cart as guest session so user sees their cart after logout
        if (!empty($cart)) {
            session()->put('cart', $cart);
            session()->put('cart_count', $cartCount);
            session()->put('selected_cart_ids', $selectedCartIds);

            if (!empty($appliedVoucher)) {
                session()->put('applied_voucher', $appliedVoucher);
            }
        }

        return redirect('/');
    }
}
