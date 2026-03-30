<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // when migrate
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')
                ->references('id')
                ->on('wallets')
                ->onDelete('cascade');  // wallet_id >> wallets.id (unsigned bigint)

            // Money going IN (credit) or OUT (debit)
            $table->enum('type', ['credit', 'debit']);

            // The exact amount of this specific transaction
            $table->decimal('amount', 10, 2);

            // Human-readable reason (e.g., "Refund for returned T-Shirt")
            $table->string('description');

            // Polymorphic relation: Creates 'reference_type' and 'reference_id' columns.
            // This links the transaction directly to the OrderItem that was returned!
            $table->nullableMorphs('reference');

            $table->timestamps();
        });
    }

    // when rollback
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
