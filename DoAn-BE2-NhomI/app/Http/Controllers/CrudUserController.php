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

public function showLogin()
{
    return view('auth.login'); // trả về file giao diện login
}

// xử lý login
public function login(Request $request)
{
    // ================== 1. VALIDATE ==================
    // bắt buộc nhập email + password, email phải đúng định dạng
    $request->validate([
        'email' => 'required|email',   // bắt buộc + đúng format email
        'password' => 'required'       // bắt buộc nhập mật khẩu
    ], [
        'email.required' => 'Vui lòng nhập email.',
        'email.email' => 'Định dạng email không hợp lệ.',
        'password.required' => 'Vui lòng nhập mật khẩu.'
    ]);

    // ================== 2. LẤY DỮ LIỆU ==================
    $email = $request->email;
    $password = $request->password;

    // ================== 3. KIỂM TRA USER ==================
    $user = \App\Models\User::where('email', $email)->first();

    if (!$user) {
        // không tồn tại email
        return back()
    ->with('error', 'Sai tài khoản hoặc mật khẩu')
    ->withInput();
    }

    // ================== 4. CHECK TÀI KHOẢN BỊ KHÓA ==================
    if ($user->is_active == 0) {
        return back()->with('error', 'Tài khoản của bạn đã bị khóa.');
    }

    // ================== 5. GHI NHỚ LOGIN ==================
    $remember = $request->has('remember');

    // ================== 6. ĐĂNG NHẬP ==================
    if (Auth::attempt([
        'email' => $email,
        'password' => $password
    ], $remember)) {

        return redirect('/')->with('success', 'Đăng nhập thành công!');
    }

    // ================== 7. SAI PASSWORD ==================
    return back()->with('error', 'Tài khoản hoặc mật khẩu không chính xác.');
}

        // thử đăng nhập bằng email
        if (
            Auth::attempt([
                'email' => $login,
                'password' => $password, // mật khẩu người dùng nhập
                'is_active' => 1      // chỉ cho login nếu active = 1
            ])
        ) {
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
    // =====================================================
// TRANG PROFILE
// =====================================================
    public function profile()
    {
        return view('auth.profile');
    }


    // =====================================================
// UPDATE PROFILE
// =====================================================
// =====================================================
// UPDATE PROFILE
// =====================================================
    // =====================================================
// UPDATE PROFILE
// =====================================================
public function updateProfile(Request $request)
{

    // =================================================
    // LẤY USER HIỆN TẠI
    // =================================================
    $user = Auth::user();



    // =================================================
    // VALIDATE
    // =================================================
    $request->validate([

        // họ tên
        'full_name' => [
            'required',
            'max:50',
            'regex:/^[\pL\s]+$/u'
        ],

        // số điện thoại
        'phone' => [
            'required',
            'digits:10',
            'regex:/^0[0-9]{9}$/'
        ],

        // avatar
        'avatar' => [

            // có thể null
            'nullable',

            // phải là file upload thật
            'file',

            // phải là ảnh thật
            'image',

            // mime type thật
            'mimetypes:image/jpeg,image/png,image/webp',

            // đuôi file
            'mimes:jpg,jpeg,png,webp',

            // > 0KB
            'min:1',

            // <= 2MB
            'max:2048'
        ],

    ], [

        // ================= FULL NAME =================
        'full_name.required'
            => 'Vui lòng nhập Họ và tên.',

        'full_name.max'
            => 'Họ tên tối đa 50 ký tự.',

        'full_name.regex'
            => 'Họ tên chứa ký tự không hợp lệ.',



        // ================= PHONE =================
        'phone.required'
            => 'Vui lòng nhập số điện thoại.',

        'phone.digits'
            => 'Số điện thoại phải gồm đúng 10 số.',

        'phone.regex'
            => 'Số điện thoại phải bắt đầu bằng số 0 và chỉ được chứa số.',



        // ================= AVATAR =================
        'avatar.file'
            => 'File tải lên không hợp lệ.',

        'avatar.image'
            => 'File tải lên phải là hình ảnh thật.',

        'avatar.mimetypes'
            => 'Định dạng ảnh không hợp lệ.',

        'avatar.mimes'
            => 'Chỉ hỗ trợ file JPG, JPEG, PNG, WEBP.',

        'avatar.min'
            => 'Dung lượng ảnh phải lớn hơn 0KB.',

        'avatar.max'
            => 'Dung lượng ảnh vượt quá 2MB.',
    ]);



    // =================================================
    // UPDATE THÔNG TIN
    // =================================================
    $user->full_name = trim($request->full_name);

    $user->phone = $request->phone;



    // =================================================
    // UPLOAD AVATAR
    // =================================================
    if ($request->hasFile('avatar'))
    {

        // lấy file
        $file = $request->file('avatar');



        // tạo tên file
        $fileName =
            time() . '_' .
            $file->getClientOriginalName();



        // folder upload
        $path = public_path('images/users');



        // nếu folder chưa có thì tạo
        if (!file_exists($path))
        {
            mkdir($path, 0777, true);
        }



        // =================================================
        // XOÁ ẢNH CŨ
        // =================================================
        if (
            $user->avatar_url &&
            file_exists(public_path($user->avatar_url))
        ) {

            unlink(public_path($user->avatar_url));
        }



        // =================================================
        // UPLOAD FILE MỚI
        // =================================================
        $file->move($path, $fileName);



        // =================================================
        // SAVE DATABASE
        // =================================================
        $user->avatar_url =
            'images/users/' . $fileName;
    }



    // =================================================
    // SAVE DATABASE
    // =================================================
    $user->save();



    // =================================================
    // SUCCESS
    // =================================================
    return back()->with(
        'success',
        'Cập nhật thông tin tài khoản thành công.'
    );
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

    User::create([
        'full_name' => $request->full_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password_hash' => \Illuminate\Support\Facades\Hash::make($request->password),
        'role' => 'user',
        'is_active' => 1    
    ]);

    return redirect('/login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
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