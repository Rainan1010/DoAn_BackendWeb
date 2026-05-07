<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyOTPMail;

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
        // Validate dữ liệu đầu vào
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.'
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Thử đăng nhập
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Kiểm tra tài khoản bị khóa
            if ($user->is_active == 0) {
                Auth::logout();
                return back()->with('error', 'Tài khoản của bạn đã bị khóa.');
            }

            // KIỂM TRA XÁC THỰC OTP
            if ($user->is_verified == 0) {
                $user_id = $user->user_id;
                Auth::logout(); // Đăng xuất để bắt buộc verify
                
                session(['otp_user_id' => $user_id]);

                // Làm mới OTP và gửi mail
                $otpCode = rand(100000, 999999);
                DB::table('otp_verifications')->updateOrInsert(
                    ['user_id' => $user_id, 'purpose' => 'register'],
                    [
                        'otp_code' => $otpCode, 
                        'expires_at' => now()->addMinutes(10), 
                        'used' => 0,
                        'created_at' => now()
                    ]
                );

                Mail::to($user->email)->send(new VerifyOTPMail($otpCode));
                return redirect()->route('otp.view')->with('error', 'Tài khoản chưa xác thực. Mã OTP mới đã được gửi qua email.');
            }

            // Chuyển hướng theo vai trò
            if ($user->role === 'admin') {
                return redirect('/admin/categories')->with('success', 'Chào mừng Admin quay trở lại!');
            }

            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->with('error', 'Tài khoản hoặc mật khẩu không chính xác.')->withInput();
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

        // Tạo User mới
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => 1,
            'is_verified' => 0,
        ]);

        // Tạo và gửi mã OTP
        $otpCode = rand(100000, 999999);
        DB::table('otp_verifications')->insert([
            'user_id' => $user->user_id,
            'otp_code' => $otpCode,
            'purpose' => 'register',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now()
        ]);

        Mail::to($user->email)->send(new VerifyOTPMail($otpCode));
        session(['otp_user_id' => $user->user_id]);

        return redirect()->route('otp.view')->with('success', 'Đăng ký thành công! Vui lòng nhập mã OTP gửi tới email.');
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

        // Xử lý tải lên Avatar
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = public_path('images/users');

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Xóa ảnh cũ để tiết kiệm bộ nhớ
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

        // Kiểm tra mật khẩu cũ
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        // Cập nhật và đăng xuất
        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        Auth::logout();
        return redirect('/login')->with('success', 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
    }
}