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
        Schema::create("product_color_images", function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained()->onDelete("cascade"); // remove images if product deleted!

            $table->string("color");
            $table->string("image_url");

            $table->boolean("is_primary")->default(false); // main image tinyint (0 || 1)
            $table->integer("order_index")->default(0); // 0 -> 1 -> 2 -> 3 etc.. for UI
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::dropIfExists("product_color_images");
    }
};
