<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('product_id');
            $table->string('image_url', 500);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false); // Cột này cực kỳ quan trọng để hiện ảnh đại diện
            $table->timestamps();

            // Khóa ngoại liên kết tới bảng products
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};