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
        Schema::table('products', function (Blueprint $table) {
            // ensuring deleting brand doesn't disturb existing product.
            $table->foreignId('brand_id')
                ->nullable()
                ->after('vendor_id')
                ->constrained('brands')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // remove foriegn key
            $table->dropForeign(['brand_id']);
            // remove column
            $table->dropColumn('brand_id');
        });
    }
};
