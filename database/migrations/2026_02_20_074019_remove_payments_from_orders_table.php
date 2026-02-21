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
        Schema::table("orders", function (Blueprint $table) {
            $table->dropColumn("payment_method");
            $table->dropColumn("payment_status");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table("orders", function (Blueprint $table) {
            $table->enum("payment_method", ["cod", "upi"]);
            $table->enum("payment_status", ["pending", "complete", "failed"]);
        });
    }
};
