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
        Schema::table("product_variants", function (Blueprint $table) {
            $table->dropColumn("color");
            $table->unsignedBigInteger("color_id");
            $table
                ->foreign("color_id")
                ->references("id")
                ->on("product_colors")
                ->onDelete("restrict"); // prevents variants removal if color from product_colors is to be removed and variant is using it.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("product_variants", function (Blueprint $table) {
            $table->dropForeign(["color_id"]);
            $table->dropColumn("color_id");
            $table->string("color");
        });
    }
};
