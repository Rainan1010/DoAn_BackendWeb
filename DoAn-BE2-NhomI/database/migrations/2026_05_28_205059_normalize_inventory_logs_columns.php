<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Nếu chưa có bảng inventory_logs thì tạo mới
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('inventory_logs')) {
            Schema::create('inventory_logs', function (Blueprint $table) {
                $table->id('log_id');

                $table->unsignedBigInteger('variant_id');
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();

                $table->string('action_type', 30)->default('import');
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

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Nếu bảng đã tồn tại thì chuẩn hóa cột
        |--------------------------------------------------------------------------
        */

        // Thêm user_id nếu thiếu
        if (!Schema::hasColumn('inventory_logs', 'user_id')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('order_id');
            });
        }

        // Nếu có action cũ thì đổi thành action_type
        if (Schema::hasColumn('inventory_logs', 'action') && !Schema::hasColumn('inventory_logs', 'action_type')) {
            DB::statement("ALTER TABLE inventory_logs CHANGE `action` `action_type` VARCHAR(30) NOT NULL DEFAULT 'import'");
        }

        // Nếu chưa có action_type thì thêm mới
        if (!Schema::hasColumn('inventory_logs', 'action_type')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->string('action_type', 30)->default('import')->after('user_id');
            });
        }

        // Nếu có quantity_after cũ thì đổi thành stock_after
        if (Schema::hasColumn('inventory_logs', 'quantity_after') && !Schema::hasColumn('inventory_logs', 'stock_after')) {
            DB::statement("ALTER TABLE inventory_logs CHANGE `quantity_after` `stock_after` INT NOT NULL DEFAULT 0");
        }

        // Nếu chưa có stock_after thì thêm mới
        if (!Schema::hasColumn('inventory_logs', 'stock_after')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->integer('stock_after')->default(0)->after('quantity_change');
            });
        }

        // Thêm created_at nếu thiếu
        if (!Schema::hasColumn('inventory_logs', 'created_at')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->timestamp('created_at')->nullable();
            });
        }

        // Thêm updated_at nếu thiếu
        if (!Schema::hasColumn('inventory_logs', 'updated_at')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Không rollback để tránh mất dữ liệu nhật ký kho
    }
};