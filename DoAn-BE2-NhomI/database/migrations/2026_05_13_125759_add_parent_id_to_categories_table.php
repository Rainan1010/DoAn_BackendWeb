<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'parent_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('category_id');
            });
        }

        // Bồi dữ liệu phân cấp nếu đang có các danh mục này
        DB::table('categories')
            ->where('slug', 'iphone')
            ->update(['parent_id' => DB::table('categories')->where('slug', 'dien-thoai')->value('category_id')]);

        DB::table('categories')
            ->where('slug', 'samsung-galaxy')
            ->update(['parent_id' => DB::table('categories')->where('slug', 'dien-thoai')->value('category_id')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'parent_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('parent_id');
            });
        }
    }
};