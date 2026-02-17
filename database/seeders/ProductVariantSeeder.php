<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // log action
        Log::info(
            "[ProductVariantSeeder@run] Product Variant Seeding initiated!",
        );

        // get product(s)
        $productMen = Product::where("name", "Cotton Levi's Tshirt")->first();
        $productWomen = Product::where(
            "name",
            "Sports Jacket for Women | Girls",
        )->first();

        // check!
        if (!$productMen) {
            // log the status
            Log::warning("Product (men) not found", [
                "status" => (bool) $productMen,
            ]);
            return;
        }
        // check
        if (!$productWomen) {
            // log the status
            Log::warning("Product (women) not found", [
                "status" => (bool) $productWomen,
            ]);
            return;
        }

        // add variant data
        $productMen->variants()->updateOrCreate(
            [
                "size" => "S",
                "color" => "red",
            ],
            [
                "price" => null,
                "stock" => 20,
                "sku" => "TSHIRT-COT-S-RED",
            ],
        );
        $productMen->variants()->updateOrCreate(
            [
                "size" => "XS",
                "color" => "red",
            ],
            [
                "price" => null,
                "stock" => 5,
                "sku" => "TSHIRT-COT-XS-RED",
            ],
        );
        // log the status
        Log::info("Added Product: $productMen->name Variants!", [
            "total" => $productMen->variants()->count(),
        ]);

        $productWomen->variants()->updateOrCreate(
            [
                "size" => "M",
                "color" => "grey",
            ],
            [
                "price" => 500.99,
                "stock" => 15,
                "sku" => "JACKET-CASUAL-M-GREY",
            ],
        );
        // log the status
        Log::info("Added Product: $productWomen->name Variants!", [
            "total" => $productWomen->variants()->count(),
        ]);

        // Log the end.
        Log::info("Product Variant Seeding done!");
    }
}
