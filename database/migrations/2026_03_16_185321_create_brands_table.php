<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // when migrate
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Organic Farm Fresh"
            $table->string('slug')->unique(); // e.g., "organic-farm-fresh"

            // For local brands, the logo might just be a photo of the shop sign or a simple icon
            $table->string('logo')->nullable();

            // A short "Story" or "About" for the local maker
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('vendor_id')->references('id')->on('users');  // vendor who owns brand
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
