<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // remove previous enum col
            $table->dropColumn('order_status');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // add it again with updated datatype
            $table->string('order_status', 50)->default('pending')->after('price');
        });
    }

    /**
     * When rollback
     */
    public function down()
    {
        // remove previous col
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('order_status');
        });
        Schema::table('order_items', function (Blueprint $table) {
            // add back previous one
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'delivered'])
                ->default('pending')
                ->after('price');
        });
    }
};
