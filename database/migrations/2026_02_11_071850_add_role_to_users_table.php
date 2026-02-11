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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'admin', 'vendor'])->default('customer')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
