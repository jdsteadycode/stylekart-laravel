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
        Schema::create("cart_items", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->foreignId("product_id")->constrained()->onDelete("cascade");
            $table
                ->foreignId("variant_id")
                ->constrained("product_variants")
                ->onDelete("cascade");
            $table->unsignedInteger("item_qty")->default(1);
            $table->timestamps();

            $table->unique(["user_id", "variant_id"]); // one variant per user cart
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop("cart_items");
    }
};
