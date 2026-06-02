<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id('product_id');
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('brand_id');
        $table->string('name', 255);
        $table->string('slug', 191)->unique();
        $table->text('description')->nullable();
        $table->decimal('base_price', 15, 0);
        $table->boolean('is_active')->default(true);
        $table->boolean('is_new')->default(false);
        $table->boolean('is_hot')->default(false);
        
        // --- BỔ SUNG CỘT NÀY ---
        $table->boolean('is_trending')->default(false);
        // -----------------------
        
        $table->integer('view_count')->default(0);
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};  