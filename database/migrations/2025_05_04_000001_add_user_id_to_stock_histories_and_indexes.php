<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('user')->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable()->after('user_id');
            $table->dropColumn('user');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('outlet_id');
            $table->index('cashier_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->unique(['product_id', 'outlet_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('barcode');
            $table->index('sku');
            $table->index('business_id');
        });

        Schema::table('stock_histories', function (Blueprint $table) {
            $table->index('stock_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'user_name']);
            $table->string('user')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['outlet_id']);
            $table->dropIndex(['cashier_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'outlet_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['barcode']);
            $table->dropIndex(['sku']);
            $table->dropIndex(['business_id']);
        });

        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropIndex(['stock_id']);
            $table->dropIndex(['created_at']);
        });
    }
};