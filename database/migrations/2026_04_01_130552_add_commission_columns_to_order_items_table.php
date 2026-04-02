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
        // when migrate,
        Schema::table('order_items', function (Blueprint $table) {
            // Adding columns after 'price' for logical ordering
            $table->decimal('admin_commission', 10, 2)->nullable()->after('price');
            $table->decimal('vendor_earning', 10, 2)->nullable()->after('admin_commission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback,
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['admin_commission', 'vendor_earning']);
        });
    }
};
