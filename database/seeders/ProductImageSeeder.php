<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Log action
        Log::info("[ProductImageSeeder@run] Product Image Seeding started");

        // get products
        $productMen = Product::where("name", "Cotton Levi's Tshirt")->first();
        $productWomen = Product::where(
            "name",
            "Sports Jacket for Women | Girls",
        )->first();

        // check
        if (!$productMen || !$productWomen) {
            // log status
            Log::warning("Products not found. Skipping image seeding.");
            return;
        }

        // add images for men product
        $productMen->images()->updateOrCreate(
            [
                "image_url" => "images/tshirt-red-front.webp",
            ],
            [
                "is_primary" => true,
                "sort_order" => 0,
            ],
        );

        $productMen->images()->updateOrCreate(
            [
                "image_url" => "images/tshirt-maroon-front.webp",
            ],
            [
                "is_primary" => false,
                "sort_order" => 1,
            ],
        );

        // Log the status
        Log::info("Images added for {$productMen->name}");

        // Add images for women product
        $productWomen->images()->updateOrCreate(
            [
                "image_url" => "images/tshirt-black-front.webp",
            ],
            [
                "is_primary" => true,
                "sort_order" => 0,
            ],
        );
        // log status
        Log::info("Images added for {$productWomen->name}");

        // log the end
        Log::info("ProductImage Seeding completed!");
    }
}
