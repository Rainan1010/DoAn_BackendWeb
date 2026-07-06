<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('stock_logs');
    }

    public function down(): void
    {
        // Không tạo lại bảng stock_logs nữa vì hệ thống đã chuyển sang inventory_logs
    }
};