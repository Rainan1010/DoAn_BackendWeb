<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CrudUserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShippingAddressController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\Auth\SocialAuthController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\OrderStatisticController;
use App\Http\Controllers\Admin\RevenueReportController;
use App\Http\Controllers\Admin\InventoryLogController;

/*
|--------------------------------------------------------------------------
| GLOBAL ROUTE PATTERNS
|--------------------------------------------------------------------------
| Chặn các route có tham số {id} nhận chuỗi không phải số.
| Ví dụ: /admin/attributes/abc sẽ trả 404 thay vì vào controller.
|--------------------------------------------------------------------------
*/

Route::pattern('id', '[0-9]+');

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search-ajax', [ProductController::class, 'searchAjax'])
    ->name('search.ajax');

Route::get('/product/{id}', [ProductController::class, 'show'])
    ->name('product.show');

Route::get('/product-detail/{id}', [HomeController::class, 'detail'])
    ->name('product.detail');

Route::get('/category/{slug}', [ProductController::class, 'category'])
    ->name('category.show');

Route::get('/promotions', [ProductController::class, 'promotions'])
    ->name('promotions.index');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [CrudUserController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [CrudUserController::class, 'login']);

    Route::get('/register', [CrudUserController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [CrudUserController::class, 'register']);

    Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])
        ->name('google.login');

    Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

    Route::get('auth/github', [SocialAuthController::class, 'redirectToGithub'])
        ->name('github.login');

    Route::get('auth/github/callback', [SocialAuthController::class, 'handleGithubCallback']);

    Route::middleware('otp.session')->group(function () {
        Route::get('/verify-otp', [OTPController::class, 'showVerifyForm'])
            ->name('otp.view');

        Route::post('/verify-otp', [OTPController::class, 'verifyOTP'])
            ->name('otp.verify');

        Route::post('/resend-otp', [OTPController::class, 'resendOTP'])
            ->name('otp.resend');
    });
});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', [CrudUserController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/password/change', [CrudUserController::class, 'showChangePassword'])
        ->name('password.change');

    Route::post('/password/change', [CrudUserController::class, 'changePassword'])
        ->name('password.update');

    Route::get('/profile', [CrudUserController::class, 'profile'])
        ->name('profile');

    Route::post('/profile/update', [CrudUserController::class, 'updateProfile'])
        ->name('profile.update');

    Route::post('/product/{id}/review', [ProductController::class, 'storeReview'])
        ->name('product.review.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */

    Route::resource('products', AdminProductController::class);

    /*
    |--------------------------------------------------------------------------
    | CATEGORIES
    |--------------------------------------------------------------------------
    */

    Route::resource('categories', CategoryController::class);

    /*
    |--------------------------------------------------------------------------
    | BRANDS
    |--------------------------------------------------------------------------
    */

    Route::view('/brands/create', 'admin.brands.create')
        ->name('brands.create');

    Route::patch('brands/{id}/toggle-status', [BrandController::class, 'toggleStatus'])
        ->name('brands.toggleStatus');

    Route::resource('brands', BrandController::class)
        ->except(['create']);

    /*
    |--------------------------------------------------------------------------
    | VOUCHERS
    |--------------------------------------------------------------------------
    */

    Route::patch('vouchers/{id}/toggle-status', [VoucherController::class, 'toggleStatus'])
        ->name('vouchers.toggleStatus');

    Route::resource('vouchers', VoucherController::class);

    /*
    |--------------------------------------------------------------------------
    | REVIEWS
    |--------------------------------------------------------------------------
    */

    Route::get('reviews', [ReviewController::class, 'index'])
        ->name('reviews.index');

    Route::get('reviews/{id}', [ReviewController::class, 'show'])
        ->name('reviews.show');

    Route::patch('reviews/{id}/status', [ReviewController::class, 'updateStatus'])
        ->name('reviews.updateStatus');

    Route::delete('reviews/{id}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy');

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    Route::resource('attributes', AttributeController::class);

    /*
    |--------------------------------------------------------------------------
    | ORDER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get('order-statistics', [OrderStatisticController::class, 'index'])
        ->name('order-statistics.index');

    Route::get('orders/create', [OrderStatisticController::class, 'create'])
        ->name('orders.create');

    Route::post('orders/store', [OrderStatisticController::class, 'store'])
        ->name('orders.store');

    Route::get('orders/search-user', [OrderStatisticController::class, 'searchUser'])
        ->name('orders.search-user');

    Route::get('orders/{id}/edit', [OrderStatisticController::class, 'edit'])
        ->name('orders.edit');

    Route::patch('orders/{id}/update', [OrderStatisticController::class, 'update'])
        ->name('orders.update');

    Route::post('orders/{id}/confirm', [OrderStatisticController::class, 'confirm'])
        ->name('orders.confirm');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware(['admin.only'])->group(function () {
        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */

        Route::patch(
            'permissions/{id}/toggle-status',
            [App\Http\Controllers\Admin\PermissionController::class, 'toggleStatus']
        )->name('permissions.toggle-status');

        Route::resource('permissions', App\Http\Controllers\Admin\PermissionController::class);

        /*
        |--------------------------------------------------------------------------
        | BACKUPS
        |--------------------------------------------------------------------------
        */

        Route::get('backups', [App\Http\Controllers\Admin\BackupController::class, 'index'])
            ->name('backups.index');

        Route::post('backups', [App\Http\Controllers\Admin\BackupController::class, 'create'])
            ->name('backups.create');

        Route::post('backups/upload', [App\Http\Controllers\Admin\BackupController::class, 'uploadRestore'])
            ->name('backups.upload');

        Route::get('backups/{id}/download', [App\Http\Controllers\Admin\BackupController::class, 'download'])
            ->name('backups.download');

        Route::post('backups/{id}/restore', [App\Http\Controllers\Admin\BackupController::class, 'restore'])
            ->name('backups.restore');

        Route::delete('backups/{id}', [App\Http\Controllers\Admin\BackupController::class, 'destroy'])
            ->name('backups.destroy');

        /*
        |--------------------------------------------------------------------------
        | REVENUE REPORTS
        |--------------------------------------------------------------------------
        */

        Route::get('/revenue-reports', [RevenueReportController::class, 'index'])
            ->name('revenue_reports.index');

        /*
        |--------------------------------------------------------------------------
        | LOGIN HISTORY
        |--------------------------------------------------------------------------
        */

        Route::get('/login-history', [CrudUserController::class, 'loginHistory'])
            ->name('login.history');

        /*
        |--------------------------------------------------------------------------
        | INVENTORY LOGS / NHẬP KHO MỚI
        |--------------------------------------------------------------------------
        | Dùng bảng chính: inventory_logs
        |--------------------------------------------------------------------------
        */

        Route::get('inventory-logs', [InventoryLogController::class, 'index'])
            ->name('inventory-logs.index');

        Route::get('inventory-logs/create', [InventoryLogController::class, 'create'])
            ->name('inventory-logs.create');

        Route::post('inventory-logs/store', [InventoryLogController::class, 'store'])
            ->name('inventory-logs.store');

        /*
        |--------------------------------------------------------------------------
        | REDIRECT ROUTE CŨ STOCK-LOGS
        |--------------------------------------------------------------------------
        | Giữ route cũ để tránh lỗi link cũ, nhưng không dùng StockLogController nữa.
        |--------------------------------------------------------------------------
        */

        Route::get('stock-logs', function () {
            return redirect()->route('admin.inventory-logs.index');
        })->name('stock-logs.index');

        Route::get('stock-logs/create', function () {
            return redirect()->route('admin.inventory-logs.create');
        })->name('stock-logs.create');
    });
});

/*
|--------------------------------------------------------------------------
| SHIPPING ADDRESS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/change-address', [ShippingAddressController::class, 'index'])
        ->name('addresses.index');

    Route::get('/change-address/create', [ShippingAddressController::class, 'create'])
        ->name('addresses.create');

    Route::post('/change-address/store', [ShippingAddressController::class, 'store'])
        ->name('addresses.store');

    Route::get('/change-address/edit/{id}', [ShippingAddressController::class, 'edit'])
        ->name('addresses.edit')
        ->where('id', '.*');

    Route::post('/change-address/update/{id}', [ShippingAddressController::class, 'update'])
        ->name('addresses.update')
        ->where('id', '.*');

    Route::delete('/change-address/delete/{id}', [ShippingAddressController::class, 'destroy'])
        ->name('addresses.destroy')
        ->where('id', '.*');

    Route::post('/change-address/default/{id}', [ShippingAddressController::class, 'setDefault'])
        ->name('addresses.default')
        ->where('id', '.*');

    Route::get('/change-address/{id}', [ShippingAddressController::class, 'edit'])
        ->name('addresses.edit_fallback')
        ->where('id', '.*');
});

/*
|--------------------------------------------------------------------------
| CART
|--------------------------------------------------------------------------
*/

Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::post('/cart/add', [CartController::class, 'add'])
    ->name('cart.add');

Route::post('/cart/update', [CartController::class, 'update'])
    ->name('cart.update');

Route::post('/cart/remove', [CartController::class, 'remove'])
    ->name('cart.remove');

Route::post('/cart/select', [CartController::class, 'select'])
    ->name('cart.select');

Route::post('/cart/toggle-select', [CartController::class, 'toggleSelect'])
    ->name('cart.toggleSelect');

Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher'])
    ->name('cart.applyVoucher');

Route::post('/cart/remove-voucher', [CartController::class, 'removeVoucher'])
    ->name('cart.removeVoucher');

/*
|--------------------------------------------------------------------------
| ORDERS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'history'])
        ->name('orders.history');

    /*
     * In hóa đơn PDF - phải đặt trước /orders/{id}
     */
    Route::get('/orders/{id}/invoice', [OrderController::class, 'invoicePdf'])
        ->name('orders.invoice');

    Route::get('/orders/{id}', [OrderController::class, 'detail'])
        ->name('orders.detail');

    Route::post('/orders/cancel/{id}', [OrderController::class, 'cancel'])
        ->name('orders.cancel');

    Route::post('/orders/reorder/{id}', [OrderController::class, 'reorder'])
        ->name('orders.reorder');
});

/*
|--------------------------------------------------------------------------
| CHECKOUT
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])
        ->name('checkout');

    Route::post('/checkout/save-information', [OrderController::class, 'saveInformation'])
        ->name('checkout.saveInformation');

    Route::get('/checkout/payment', [OrderController::class, 'payment'])
        ->name('checkout.payment');

    Route::post('/checkout/store', [OrderController::class, 'store'])
        ->name('checkout.store');

    /*
    |--------------------------------------------------------------------------
    | MOMO
    |--------------------------------------------------------------------------
    */

    Route::post('/payment/momo', [OrderController::class, 'momoPayment'])
        ->name('payment.momo');

    Route::get('/momo/return', [OrderController::class, 'momoReturn'])
        ->name('momo.return');

    Route::post('/momo/ipn', [OrderController::class, 'momoIPN'])
        ->name('momo.ipn');

    /*
    |--------------------------------------------------------------------------
    | VNPAY
    |--------------------------------------------------------------------------
    */

    Route::post('/payment/vnpay', [OrderController::class, 'vnpayPayment'])
        ->name('payment.vnpay');

    Route::get('/vnpay-portal', [OrderController::class, 'vnpayMockPortal'])
        ->name('vnpay.mock_portal');

    Route::get('/vnpay/return', [OrderController::class, 'vnpayReturn'])
        ->name('vnpay.return');
});

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

Route::get('/api/compare-product/{id}', [App\Http\Controllers\CompareController::class, 'getCompareProduct']);

// Compare routes (session-based)
Route::post('/compare/add', [App\Http\Controllers\CompareController::class, 'add'])->name('compare.add');
Route::post('/compare/remove', [App\Http\Controllers\CompareController::class, 'remove'])->name('compare.remove');
Route::post('/compare/clear', [App\Http\Controllers\CompareController::class, 'clear'])->name('compare.clear');
Route::get('/compare', [App\Http\Controllers\CompareController::class, 'index'])->name('compare.index');

Route::get('/api/prices/sync', [App\Http\Controllers\Api\ProductPriceController::class, 'sync'])
    ->name('api.prices.sync');

/*
|--------------------------------------------------------------------------
| AJAX SHIPPING FEE
|--------------------------------------------------------------------------
*/

Route::post('/get-shipping-fee', [OrderController::class, 'getShippingFeeAjax'])
    ->name('shipping.fee');