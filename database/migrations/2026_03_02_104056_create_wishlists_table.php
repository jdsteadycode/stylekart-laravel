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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade'); // user deleted, remove the record
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade'); // product deleted, remove the record
            $table->foreignId('variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('cascade'); // variant deleted, remove the record
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'variant_id']); // prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
