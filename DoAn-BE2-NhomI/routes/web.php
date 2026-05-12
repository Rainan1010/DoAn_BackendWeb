<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\CrudUserController;

// Khai báo đường dẫn trang chủ 
Route::get('/', [HomeController::class, 'index'])->name('home');
// Route dành riêng cho Ajax tìm kiếm
Route::get('/search-ajax', [App\Http\Controllers\ProductController::class, 'searchAjax'])->name('search.ajax');
// Route hiển thị trang chi tiết sản phẩm
Route::get('/product/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('product.show');

// ---------------------------------------------------
// Các route của Trung
// ---------------------------------------------------

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    
    // Quản lý Thương hiệu
    Route::patch('brands/{brand}/toggle-status', [App\Http\Controllers\Admin\BrandController::class, 'toggleStatus'])->name('brands.toggleStatus');
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class);
});

// ---------------------------------------------------
// Các route của Trang, Thực sẽ viết tiếp xuống đây...
// ---------------------------------------------------
// hiển thị form login
Route::get('/login', [CrudUserController::class, 'showLogin'])->name('login');

// xử lý login khi submit form
Route::post('/login', [CrudUserController::class, 'login']);

// hiển thị form đăng ký
Route::get('/register', [CrudUserController::class, 'showRegister']);
// xử lý đăng ký
Route::post('/register', [CrudUserController::class, 'register']);

//chi tiết sản phẩm
Route::get('/product/{id}', [HomeController::class, 'detail']);



//xử lý logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');


// hiển thị form đổi mật khẩu
Route::get('/password/change', [CrudUserController::class, 'showChangePassword'])->middleware('auth');

// xử lý đổi mật khẩu
Route::post('/password/change', [CrudUserController::class, 'changePassword'])->middleware('auth');


// quản lí Voucher 
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('vouchers', App\Http\Controllers\Admin\VoucherController::class);
});
