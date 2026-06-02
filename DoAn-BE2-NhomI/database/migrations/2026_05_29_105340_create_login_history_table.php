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
        Schema::create('login_history', function (Blueprint $table) {
            $table->bigIncrements('history_id');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email', 100)->nullable();

            $table->timestamp('login_time')->nullable()->useCurrent();
            $table->timestamp('logout_time')->nullable();

            $table->string('ip_address', 50)->nullable();

            $table->enum('status', [
                'success',
                'failed'
            ])->default('success');

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')
                  ->nullable()
                  ->useCurrent()
                  ->useCurrentOnUpdate();

            // FK đúng với bảng users của bạn
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_history');
    }
};