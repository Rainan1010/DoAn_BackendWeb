<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\CrudUserController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\OTPController;



/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Trang chủ & Sản phẩm)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search-ajax', [ProductController::class, 'searchAjax'])->name('search.ajax');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/product-detail/{id}', [HomeController::class, 'detail'])->name('product.detail');


/*
|--------------------------------------------------------------------------
| AUTHENTICATION (Đăng nhập, Đăng ký, Đăng xuất)
|--------------------------------------------------------------------------
*/

// ---------------------------------------------------
// Các route của Trung
// ---------------------------------------------------

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    
    // Quản lý Thương hiệu
    Route::patch('brands/{id}/toggle-status', [App\Http\Controllers\Admin\BrandController::class, 'toggleStatus'])->name('brands.toggleStatus');
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class);
});

// ---------------------------------------------------
// Các route của Trang, Thực sẽ viết tiếp xuống đây...
// ---------------------------------------------------
// Chặn người đã đăng nhập vào lại các trang auth
Route::middleware('guest')->group(function () {
    // hiển thị form login + xử lý login khi submit form
    Route::get('/login', [CrudUserController::class, 'showLogin'])->name('login');
    Route::post('/login', [CrudUserController::class, 'login']);

    Route::get('/register', [CrudUserController::class, 'showRegister'])->name('register');
    Route::post('/register', [CrudUserController::class, 'register']);

    //Login Google & Github
    Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

    Route::get('auth/github', [SocialAuthController::class, 'redirectToGithub'])->name('github.login');
    Route::get('auth/github/callback', [SocialAuthController::class, 'handleGithubCallback']);

    //Xác thực OTP - Bắt buộc phải có session đăng ký mới được vào
    Route::middleware('otp.session')->group(function () {
        Route::get('/verify-otp', [OTPController::class, 'showVerifyForm'])->name('otp.view');
        Route::post('/verify-otp', [OTPController::class, 'verifyOTP'])->name('otp.verify');
        Route::post('/resend-otp', [OTPController::class, 'resendOTP'])->name('otp.resend');
    });
});

//chi tiết sản phẩm
Route::get('/product/{id}', [HomeController::class, 'detail']);



//xử lý logout

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');


/*
|--------------------------------------------------------------------------
| USER ROUTES (Cần đăng nhập - Đổi mật khẩu)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [CrudUserController::class, 'showChangePassword'])->name('password.change');
    Route::post('/password/change', [CrudUserController::class, 'changePassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Quản trị viên - Có Prefix 'admin' và Name 'admin.')
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    // Quản lý Danh mục (Categories)
    Route::resource('categories', CategoryController::class);

    // Quản lý Thương hiệu (Brands)
    Route::view('/brands/create', 'admin.brands.create')->name('brands.create'); // Ưu tiên route cụ thể trước resource
    Route::patch('brands/{id}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggleStatus');
    Route::resource('brands', BrandController::class);
    
    // Quản lý Voucher (Vouchers)
    Route::patch('vouchers/{id}/toggle-status', [VoucherController::class, 'toggleStatus'])->name('vouchers.toggleStatus');
    Route::resource('vouchers', VoucherController::class);

});


// hiển thị form đổi mật khẩu
Route::get('/password/change', [CrudUserController::class, 'showChangePassword'])->middleware('auth');

// xử lý đổi mật khẩu
Route::post('/password/change', [CrudUserController::class, 'changePassword'])->middleware('auth');


