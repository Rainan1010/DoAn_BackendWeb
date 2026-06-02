<?php


namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Chia sẻ danh mục sản phẩm active (chỉ lấy danh mục cha) cho layout
        View::composer('layouts.app', function ($view) {
            $categories = Category::where('is_active', 1)
                ->where(function ($query) {
                    $query->whereNull('parent_id')
                          ->orWhere('parent_id', 0);
                })
                ->orderBy('sort_order', 'asc')
                ->get();
            $view->with('categories', $categories);
        });
    }
}
