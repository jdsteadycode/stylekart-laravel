<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // when migrate
    public function up(): void
    {
        Schema::create('delivery_jobs', function (Blueprint $table) {
            $table->id();

            // Is this a regular delivery or a return?
            $table->enum('job_type', ['delivery', 'return']);

            // The ID of either the 'Order' (for delivery) or 'OrderItem' (for return)
            $table->unsignedBigInteger('reference_id');

            // Geographic data for the Gig Board matching
            $table->string('pickup_city');
            $table->string('dropoff_city');

            // Full JSON addresses so the driver doesn't need to join 3 tables to see where to go
            $table->json('pickup_address');
            $table->json('dropoff_address');

            // The Lifecycle
            $table->string('status');

            // The Driver (Nullable until someone accepts it)
            $table->foreignId('delivery_person_id')->nullable()->constrained('users')->nullOnDelete();

            // How much the driver makes for this specific trip
            $table->decimal('earnings', 8, 2)->default(0.00);

            $table->timestamps();
        });
    }

    // when rollback
    public function down(): void
    {
        Schema::dropIfExists('delivery_jobs');
    }
};
