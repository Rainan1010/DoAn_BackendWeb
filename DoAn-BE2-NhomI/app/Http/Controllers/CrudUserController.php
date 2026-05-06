<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request; // dùng để lấy dữ liệu từ form
use Illuminate\Support\Facades\Auth; // dùng cho login/logout
use Illuminate\Support\Facades\Hash; // đổi mật khẩu 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyOTPMail;

class CrudUserController extends Controller
{
    // hiển thị form login
public function showLogin()
{
    return view('auth.login'); // trả về file giao diện login
}

// xử lý login
public function login(Request $request)
{
    // lấy dữ liệu người dùng nhập
    $login = $request->identifier; // email hoặc username
    $password = $request->password; // mật khẩu

    // thử đăng nhập bằng email
    if (Auth::attempt([
        'email' => $login,
        'password' => $password, // mật khẩu người dùng nhập
        'is_active' => 1      // chỉ cho login nếu active = 1
    ])) {
        $user = Auth::user();
        
        // Nếu là admin -> vào trang quản trị
        if ($user->role === 'admin') {
            return redirect('/admin/categories');
        }

        // kiểm tra role, nếu là admin thì chuyển đến trang quản lý danh mục
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/categories');
        }

        // nếu đúng → chuyển về trang chủ
        return redirect('/');
    }

    // nếu sai → quay lại form + báo lỗi
    return back()->with('error', 'Sai tài khoản hoặc mật khẩu');
}

public function showRegister()
{
    return view('auth.register');
}

public function register(Request $request)
{
    $request->validate([
        'full_name' => 'required|string|max:100',
        'email' => 'required|string|email|max:100|unique:users',
        'phone' => 'required|string|max:20',
        'password' => 'required|string|min:6|confirmed',
    ], [
        'full_name.required' => 'Vui lòng nhập họ và tên.',
        'email.required' => 'Vui lòng nhập email.',
        'email.email' => 'Email không hợp lệ.',
        'email.unique' => 'Email này đã được đăng ký.',
        'phone.required' => 'Vui lòng nhập số điện thoại.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
    ]);

    // 1. Tạo User
    $user = User::create([
        'full_name' => $request->full_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password_hash' => Hash::make($request->password),
        'role' => 'user',
        'is_active' => 1,
        'is_verified' => 0, // Chưa xác thực
    ]);

    // 2. Tạo mã OTP 6 số
    $otpCode = rand(100000, 999999);
    
    DB::table('otp_verifications')->insert([
        'user_id' => $user->user_id,
        'otp_code' => $otpCode,
        'purpose' => 'register',
        'expires_at' => now()->addMinutes(10),
    ]);

    // 3. Gửi Mail qua Mailpit
    Mail::to($user->email)->send(new VerifyOTPMail($otpCode));

    // 4. Lưu ID vào session để biết đang xác thực cho ai
    session(['otp_user_id' => $user->user_id]);

    return redirect()->route('otp.view');
}
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
        'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
        'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
        'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
    ]);

    $user = Auth::user();

    // kiểm tra mật khẩu hiện tại
    if (!Hash::check($request->current_password, $user->password_hash)) {
        return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
    }

    // cập nhật mật khẩu mới
    $user->password_hash = Hash::make($request->new_password);
    $user->save();

    // đăng xuất session cũ để tránh lỗi
    Auth::logout();

    // chuyển về trang login
    return redirect('/login')->with('success', 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
}


}