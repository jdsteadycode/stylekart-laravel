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
        Schema::create("order_items", function (Blueprint $table) {
            $table->id();
            $table->foreignId("order_id")->constrained()->onDelete("cascade");
            $table->foreignId("product_id")->constrained()->onDelete("cascade"); // which product's
            $table
                ->foreignId("variant_id")
                ->constrained("product_variants")
                ->onDelete("cascade"); // which product's actual selling unit
            $table
                ->foreignId("vendor_id")
                ->constrained("users")
                ->onDelete("cascade"); // from which vendor (whom)
            $table->integer("quantity");
            $table->decimal("price", 10, 2);
            $table
                ->enum("status", [
                    "pending",
                    "processing",
                    "shipped",
                    "delivered",
                    "cancelled",
                ])
                ->default("pending"); // current status of to-be ordered item
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
