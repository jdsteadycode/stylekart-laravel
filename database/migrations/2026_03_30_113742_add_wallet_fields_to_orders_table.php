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
        // when migrate
        Schema::table('orders', function (Blueprint $table) {
            // 💰 How much was taken from the wallet for this order
            $table->decimal('wallet_amount_used', 10, 2)
                ->default(0.00)
                ->after('total_amount');

            // 💳 The remaining amount to be paid via Payment Mode (COD/ONLINE - MOCK PAYMENT SERVICE)
            // This is (Total - Wallet)
            $table->decimal('payable_amount', 10, 2)
                ->default(0.00)
                ->after('wallet_amount_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('wallet_amount_used');
            $table->dropColumn('payable_amount');
        });
    }
};
