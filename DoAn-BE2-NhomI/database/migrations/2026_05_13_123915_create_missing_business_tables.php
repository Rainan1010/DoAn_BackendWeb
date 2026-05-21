<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. carts
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id('cart_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('session_id', 255)->nullable();
                $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

                $table->index('user_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 2. cart_items
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id('item_id');
                $table->unsignedBigInteger('cart_id');
                $table->unsignedBigInteger('variant_id');
                $table->integer('quantity');
                $table->decimal('price', 15, 0);

                $table->index('cart_id');
                $table->index('variant_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 3. shipping_addresses
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('shipping_addresses')) {
            Schema::create('shipping_addresses', function (Blueprint $table) {
                $table->id('address_id');
                $table->unsignedBigInteger('user_id');
                $table->string('full_name', 255);
                $table->string('phone', 20);
                $table->string('province', 100);
                $table->string('district', 100);
                $table->string('ward', 100);
                $table->string('street_address', 500);
                $table->boolean('is_default')->default(false);

                $table->index('user_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 4. shipping_fees
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('shipping_fees')) {
            Schema::create('shipping_fees', function (Blueprint $table) {
                $table->id('fee_id');
                $table->string('province', 100);
                $table->decimal('fee', 10, 0);
                $table->integer('estimated_days');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 5. vouchers
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('vouchers')) {
            Schema::create('vouchers', function (Blueprint $table) {
                $table->id('voucher_id');
                $table->string('code', 50)->unique();
                $table->enum('type', ['percent', 'fixed']);
                $table->decimal('value', 10, 2);
                $table->decimal('min_order_value', 15, 0);
                $table->decimal('max_discount', 15, 0)->nullable();
                $table->integer('usage_limit')->nullable();
                $table->integer('used_count')->default(0);
                $table->timestamp('start_at')->nullable();
                $table->timestamp('end_at')->nullable();
                $table->boolean('is_active')->default(true);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 6. orders
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id('order_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('shipping_address_id');
                $table->unsignedBigInteger('voucher_id')->nullable();
                $table->string('order_code', 50)->unique();
                $table->decimal('subtotal', 15, 0);
                $table->decimal('shipping_fee', 10, 0);
                $table->decimal('discount_amount', 15, 0)->default(0);
                $table->decimal('total_amount', 15, 0);
                $table->enum('payment_method', ['cod', 'vnpay', 'momo']);
                $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
                $table->enum('order_status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
                $table->text('cancel_reason')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();

                $table->index('user_id');
                $table->index('shipping_address_id');
                $table->index('voucher_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 7. order_items
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id('order_item_id');
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('variant_id');
                $table->string('product_name', 255);
                $table->string('variant_info', 255)->nullable();
                $table->decimal('unit_price', 15, 0);
                $table->integer('quantity');
                $table->decimal('subtotal', 15, 0);

                $table->index('order_id');
                $table->index('variant_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 8. payments
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id('payment_id');
                $table->unsignedBigInteger('order_id');
                $table->enum('gateway', ['cod', 'vnpay', 'momo']);
                $table->string('transaction_id', 191)->nullable()->unique();
                $table->decimal('amount', 15, 0);
                $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
                $table->json('gateway_response')->nullable();
                $table->timestamp('paid_at')->nullable();

                $table->index('order_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 9. inventory_logs
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('inventory_logs')) {
            Schema::create('inventory_logs', function (Blueprint $table) {
                $table->id('log_id');
                $table->unsignedBigInteger('variant_id');
                $table->unsignedBigInteger('order_id')->nullable();
                $table->enum('action', ['import', 'export', 'adjust', 'return']);
                $table->integer('quantity_change');
                $table->integer('quantity_after');
                $table->string('note', 500)->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamp('created_at')->nullable()->useCurrent();

                $table->index('variant_id');
                $table->index('order_id');
                $table->index('created_by');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 10. price_histories
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('price_histories')) {
            Schema::create('price_histories', function (Blueprint $table) {
                $table->id('history_id');
                $table->unsignedBigInteger('variant_id');
                $table->decimal('old_price', 15, 0);
                $table->decimal('new_price', 15, 0);
                $table->unsignedBigInteger('changed_by');
                $table->timestamp('changed_at')->nullable()->useCurrent();

                $table->index('variant_id');
                $table->index('changed_by');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 11. revenue_reports
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('revenue_reports')) {
            Schema::create('revenue_reports', function (Blueprint $table) {
                $table->id('report_id');
                $table->date('report_date');
                $table->decimal('total_revenue', 15, 0);
                $table->integer('total_orders');
                $table->integer('total_items_sold');
                $table->decimal('avg_order_value', 15, 0);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 12. backup_logs
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('backup_logs')) {
            Schema::create('backup_logs', function (Blueprint $table) {
                $table->id('backup_id');
                $table->string('file_name', 255);
                $table->string('file_path', 500);
                $table->bigInteger('file_size');
                $table->enum('status', ['success', 'failed']);
                $table->unsignedBigInteger('created_by');
                $table->timestamp('created_at')->nullable()->useCurrent();

                $table->index('created_by');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 13. otp_verifications
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('otp_verifications')) {
            Schema::create('otp_verifications', function (Blueprint $table) {
                $table->id('otp_id');
                $table->unsignedBigInteger('user_id');
                $table->string('otp_code', 10);
                $table->enum('purpose', ['register', 'login', '2fa']);
                $table->timestamp('expires_at')->nullable();
                $table->boolean('used')->default(false);

                $table->index('user_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 14. password_resets
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->id('reset_id');
                $table->unsignedBigInteger('user_id');
                $table->string('token', 191)->unique();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('used')->default(false);

                $table->index('user_id');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 15. remember_tokens
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('remember_tokens')) {
            Schema::create('remember_tokens', function (Blueprint $table) {
                $table->id('token_id');
                $table->unsignedBigInteger('user_id');
                $table->string('token', 191)->unique();
                $table->timestamp('expires_at')->nullable();

                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Không drop bảng để tránh mất dữ liệu khi rollback nhầm.
        |--------------------------------------------------------------------------
        */
    }
};