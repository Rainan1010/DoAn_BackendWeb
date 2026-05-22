<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id('variant_id');
            $table->unsignedBigInteger('product_id');
            $table->string('sku')->unique(); // Mã kho hàng (ví dụ: IP17-GOLD-256)
            $table->decimal('price', 15, 0);
            $table->decimal('sale_price', 15, 0)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->json('attribute_values')->nullable(); // Lưu: {"Color": "Gold", "RAM": "8GB"}
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Khóa ngoại liên kết tới bảng products
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};