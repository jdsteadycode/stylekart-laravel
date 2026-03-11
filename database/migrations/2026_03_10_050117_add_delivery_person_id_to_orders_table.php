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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_person_id')    // a new unsigned integer column
                ->nullable()    // ensuring customer can safely place order
                ->references('id')  // points to id column
                ->on('users')   // of relation (table) - users
                ->after('user_id'); // place it after user_id in orders (this) relation || table.

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_person_id']); // first remove foreign key
            $table->dropColumn('delivery_person_id');  // then, drop column
        });
    }
};
