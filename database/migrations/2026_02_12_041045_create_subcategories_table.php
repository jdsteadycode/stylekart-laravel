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
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id(); // subcategory id
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete(); // ensures deletion when parent category is deleted
            $table->string('name'); // e.g., Casual Shoes, Jackets
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['category_id', 'name']); // unique subcategory per category
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};
