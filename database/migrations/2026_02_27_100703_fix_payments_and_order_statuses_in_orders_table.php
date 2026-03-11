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
        // remove the columns
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn(['order_status', 'payment_mode', 'payment_status']);
        });

        // add again
        Schema::table('orders', function (Blueprint $table) {

            // save as enum
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'delivered'])
                ->default('pending')
                ->after('total_amount');

            $table->enum('payment_mode', ['cod', 'online'])
                ->nullable()
                ->after('order_status');

            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                ->default('pending')
                ->after('payment_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_status', 'payment_mode', 'payment_status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_status')->default('pending')->after('total_amount');
            $table->string('payment_mode')->nullable()->after('order_status');
            $table->string('payment_status')->default('pending')->after('payment_mode');
        });
    }
};
