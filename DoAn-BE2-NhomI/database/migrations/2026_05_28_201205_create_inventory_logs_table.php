<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_logs')) {
            Schema::create('inventory_logs', function (Blueprint $table) {
                $table->id('log_id');

                $table->unsignedBigInteger('variant_id');
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();

                $table->string('action_type', 30);
                $table->integer('quantity_change');
                $table->integer('stock_after')->default(0);
                $table->text('note')->nullable();

                $table->timestamps();

                $table->foreign('variant_id')
                    ->references('variant_id')
                    ->on('product_variants')
                    ->onDelete('cascade');

                $table->foreign('order_id')
                    ->references('order_id')
                    ->on('orders')
                    ->onDelete('set null');

                $table->foreign('user_id')
                    ->references('user_id')
                    ->on('users')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};