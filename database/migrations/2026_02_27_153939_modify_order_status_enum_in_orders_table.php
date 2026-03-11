<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // when migrate
    public function up(): void
    {
        DB::statement("
            ALTER TABLE orders
            MODIFY order_status VARCHAR(50)
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // when rollback
        DB::statement("
            ALTER TABLE orders
            MODIFY order_status
            ENUM('pending','processing','shipped','delivered')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
