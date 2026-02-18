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
            // drop the columns
            $table->dropColumn("image_url");
            $table->dropColumn("is_primary");
            $table->dropColumn("order_index");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when roll back
        Schema::table("product_color_images", function (Blueprint $table) {
            // re-create the columns..
            $table->string("image_url");
            $table->boolean("is_primary")->default(false); // main image tinyint (0 || 1)
            $table->integer("order_index")->default(0); // 0 -> 1 -> 2 -> 3 etc.. for UI
        });
    }
};
