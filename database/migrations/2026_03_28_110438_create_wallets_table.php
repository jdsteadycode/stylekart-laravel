<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // when migrate
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');  // user_id -> users.id (as bigint unsigned)

            // 10 digits total, 2 after the decimal (e.g., 99999999.99)
            $table->decimal('balance', 10, 2)->default(0.00);

            $table->timestamps();   // created_at, updated_at
        });
    }

    // when rollback
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
