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
        Schema::table("product_colors", function (Blueprint $table) {
            $table->renameColumn("color", "name");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table("product_colors", function (Blueprint $table) {
            $table->renameColumn("name", "color");
        });
    }
};
