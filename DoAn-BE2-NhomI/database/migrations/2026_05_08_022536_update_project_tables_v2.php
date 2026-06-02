<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Cập nhật bảng users (Social Login)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider', 50)->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable()->after('provider');
            }
        });

        // 2. Cập nhật bảng categories
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'version')) {
                    $table->integer('version')->default(1)->after('is_active');
                }
            });
        }

        // 3. Tạo bảng review_images
        if (!Schema::hasTable('review_images')) {
            Schema::create('review_images', function (Blueprint $table) {
                $table->id('image_id');
                $table->unsignedBigInteger('review_id'); 
                $table->string('image_url', 500);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->index('review_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('review_images');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_id']);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('version');
        });
    }
};