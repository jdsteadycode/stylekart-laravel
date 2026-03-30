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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('return_status')->nullable()->after('cancel_reason');
            $table->text('return_reason')->nullable()->after('return_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('return_status');
            $table->dropColumn('return_reason');
        });
    }
};
