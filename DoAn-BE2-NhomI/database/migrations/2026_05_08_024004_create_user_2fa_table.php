<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_2fa', function (Blueprint $table) {
            $table->id('tfa_id'); // Khóa chính
            $table->unsignedBigInteger('user_id'); // Liên kết với user_id của bảng users
            $table->string('secret_key', 255);
            $table->boolean('is_enabled')->default(false);
            $table->timestamp('created_at')->nullable();
            
            // Thiết lập ràng buộc khóa ngoại để đảm bảo an toàn dữ liệu
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_2fa');
    }
};  