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
        Schema::table("product_color_images", function (Blueprint $table) {
            $table->unique(["product_id", "color"], "product_color_unique");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table("product_color_images", function (Blueprint $table) {
            $table->dropUnique("product_color_unique");
        });
    }
};
