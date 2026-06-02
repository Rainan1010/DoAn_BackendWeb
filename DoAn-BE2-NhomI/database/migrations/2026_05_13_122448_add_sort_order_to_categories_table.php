<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Bồi thêm icon_url nếu database hiện tại chưa có
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasColumn('categories', 'icon_url')) {
            Schema::table('categories', function (Blueprint $table) {
                if (Schema::hasColumn('categories', 'slug')) {
                    $table->string('icon_url', 500)->nullable()->after('slug');
                } else {
                    $table->string('icon_url', 500)->nullable();
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Bồi thêm sort_order nếu database hiện tại chưa có
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasColumn('categories', 'sort_order')) {
            Schema::table('categories', function (Blueprint $table) {
                if (Schema::hasColumn('categories', 'icon_url')) {
                    $table->integer('sort_order')->default(0)->after('icon_url');
                } else {
                    $table->integer('sort_order')->default(0);
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Bồi dữ liệu sort_order cho các danh mục đang có
        |--------------------------------------------------------------------------
        */
        DB::table('categories')->where('slug', 'dien-thoai')->update(['sort_order' => 1]);
        DB::table('categories')->where('slug', 'laptop')->update(['sort_order' => 2]);
        DB::table('categories')->where('slug', 'phu-kien')->update(['sort_order' => 3]);
        DB::table('categories')->where('slug', 'iphone')->update(['sort_order' => 1]);
        DB::table('categories')->where('slug', 'samsung-galaxy')->update(['sort_order' => 2]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'sort_order')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }

        if (Schema::hasColumn('categories', 'icon_url')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('icon_url');
            });
        }
    }
};