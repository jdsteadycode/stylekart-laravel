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
        Schema::create("products", function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId("vendor_id")
                ->constrained("users")
                ->cascadeOnDelete();

            $table
                ->foreignId("sub_category_id")
                ->constrained()
                ->cascadeOnDelete();

            $table->string("name");
            $table->text("description")->nullable();

            $table->decimal("base_price", 10, 2);

            $table
                ->enum("status", ["pending", "approved", "rejected"])
                ->default("pending");

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("products");
    }
};
