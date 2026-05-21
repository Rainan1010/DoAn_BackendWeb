<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('brands', function (Blueprint $table) {
        // Sửa từ $table->id(); thành dòng dưới đây:
        $table->id('brand_id'); 
        
        $table->string('name', 100);
        $table->string('slug', 100);
        $table->string('logo_url', 191)->nullable();
        $table->text('description')->nullable();
        $table->string('country', 50)->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
