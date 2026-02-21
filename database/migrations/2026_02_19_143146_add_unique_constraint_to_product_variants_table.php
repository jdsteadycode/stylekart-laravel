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
        Schema::table("product_variants", function (Blueprint $table) {
            $table->unique(
                ["product_id", "color_id", "size"],
                "product_variant_unique_combination",
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::table("product_variants", function (Blueprint $table) {
            $table->dropUnique("product_variant_unique_combination");
        });
    }
};
