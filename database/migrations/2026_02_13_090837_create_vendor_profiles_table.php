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
        Schema::create("vendor_profiles", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("user_id")
                ->constrained()
                ->cascadeOnDelete()
                ->unique(); // strict 1:1

            $table->string("shop_name");
            $table->text("shop_address");
            $table->string("phone");

            $table
                ->enum("status", ["pending", "approved", "rejected"])
                ->default("pending");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // when rollback..
        Schema::dropIfExists("vendor_profiles");
    }
};
