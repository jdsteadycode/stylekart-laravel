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
            $table->string('delivery_otp')->nullable()->after('delivery_person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_otp');
        });
    }
};
