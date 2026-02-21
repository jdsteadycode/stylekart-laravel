<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // when migrate
        Schema::table("order_items", function (Blueprint $table) {
            // drop the status column
            $table->dropColumn("status");

            // order-status column
            $table
                ->enum("order_status", [
                    "pending",
                    "processing",
                    "shipped",
                    "delivered",
                    "cancelled",
                ])
                ->after("price");

            // add the payment method after order-status
            $table
                ->enum("payment_mode", ["cod", "online"])
                ->after("order_status");

            // add the payment status after payment method
            $table
                ->enum("payment_status", [
                    "pending",
                    "paid",
                    "failed",
                    "refunded",
                ])
                ->after("payment_mode");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table("order_items", function (Blueprint $table) {
            $table->dropColumn("payment_status");
            $table->dropColumn("payment_method");
            $table->dropColumn("order_status");
            $table->enum("status", [
                "pending",
                "processing",
                "shipped",
                "delivered",
                "cancelled",
            ]);
        });
    }
};
