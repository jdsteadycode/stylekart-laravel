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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();

            // Who owns this discount? (Mayank, Kevin, etc.)
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');

            // Name of the sale
            $table->string('name'); // e.g., "Sunday Special"

            // What kind of discount?
            $table->enum('discount_type', ['percentage', 'fixed_amount']);
            $table->decimal('discount_value', 8, 2); // e.g., 15.00 for 15%, or 100.00 for ₹100

            // TARGETS (Both nullable! The vendor only fills one of these)
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained()->onDelete('cascade');

            // The Timer!
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');

            // Manual kill switch
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
