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
        // Add columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_status')
                ->default('pending')
                ->after('total_amount');

            $table->string('payment_mode')
                ->nullable()
                ->after('order_status');

            $table->string('payment_status')
                ->default('pending')
                ->after('payment_mode');
        });

        // Remove payment columns from order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['payment_mode', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('payment_mode')->nullable();
            $table->string('payment_status')->default('pending');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_status', 'payment_mode', 'payment_status']);
        });
    }
};
