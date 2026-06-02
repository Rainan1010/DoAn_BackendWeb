<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_logs')) {
            return;
        }

        // Nếu có cả created_by và user_id, chuyển dữ liệu created_by sang user_id trước khi xóa
        if (
            Schema::hasColumn('inventory_logs', 'created_by') &&
            Schema::hasColumn('inventory_logs', 'user_id')
        ) {
            DB::statement("
                UPDATE inventory_logs
                SET user_id = created_by
                WHERE user_id IS NULL AND created_by IS NOT NULL
            ");
        }

        // Xóa foreign key của created_by nếu có
        if (Schema::hasColumn('inventory_logs', 'created_by')) {
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'inventory_logs'
                  AND COLUMN_NAME = 'created_by'
                  AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE inventory_logs DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
            }

            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('inventory_logs')) {
            return;
        }

        if (!Schema::hasColumn('inventory_logs', 'created_by')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->after('note');
            });
        }
    }
};