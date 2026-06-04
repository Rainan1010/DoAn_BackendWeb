<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to change start_at and end_at to datetime.
     */
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dateTime('start_at')->nullable()->change();
            $table->dateTime('end_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->timestamp('start_at')->nullable()->change();
            $table->timestamp('end_at')->nullable()->change();
        });
    }
};
