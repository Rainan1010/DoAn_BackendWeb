<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('inventory_logs', 'user_id')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('order_id');
            });
        }

        if (Schema::hasColumn('inventory_logs', 'action') && !Schema::hasColumn('inventory_logs', 'action_type')) {
            DB::statement("ALTER TABLE inventory_logs CHANGE `action` `action_type` VARCHAR(30) NOT NULL DEFAULT 'import'");
        }

        if (!Schema::hasColumn('inventory_logs', 'action_type')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->string('action_type', 30)->default('import')->after('user_id');
            });
        }

        if (Schema::hasColumn('inventory_logs', 'quantity_after') && !Schema::hasColumn('inventory_logs', 'stock_after')) {
            DB::statement("ALTER TABLE inventory_logs CHANGE `quantity_after` `stock_after` INT NOT NULL DEFAULT 0");
        }

        if (!Schema::hasColumn('inventory_logs', 'stock_after')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->integer('stock_after')->default(0)->after('quantity_change');
            });
        }

        if (!Schema::hasColumn('inventory_logs', 'created_at')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasColumn('inventory_logs', 'updated_at')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        //
    }
};