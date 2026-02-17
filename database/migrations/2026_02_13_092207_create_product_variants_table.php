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
        Schema::create("product_variants", function (Blueprint $table) {
            $table->id();

            $table->foreignId("product_id")->constrained()->cascadeOnDelete();

            $table->string("size")->nullable();
            $table->string("color")->nullable();

            $table->string("sku")->unique()->nullable();

            $table->decimal("price", 10, 2)->nullable();

            $table->unsignedInteger("stock")->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback
        Schema::dropIfExists("product_variants");
    }
};
