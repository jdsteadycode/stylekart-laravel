<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // log the action
        Log::info("[ProductSeeder@run] Seeding of Product initiated!");

        // get the vendor(s) (user)
        $kevin = User::where("email", "kevin@gmail.com")->first();
        // Log the status
        if ($kevin) {
            Log::info("User $kevin->name", ["status" => (bool) $kevin]);
        } else {
            Log::warning("Kevin user not found");
        }

        $mayank = User::where("email", "mayank@gmail.com")->first();
        // Log the status
        if ($mayank) {
            Log::info("User $mayank->name", ["status" => (bool) $mayank]);
        } else {
            Log::warning("mayank user not found");
        }

        // get the sub category(s)
        $topWearMen = SubCategory::where("id", 10)->first();
        $topWearWomen = SubCategory::where("id", 11)->first();

        // Log the status
        if ($topWearMen) {
            Log::info("Subcategory (Men) $topWearMen->name", [
                "status" => (bool) $topWearMen,
            ]);
        } else {
            Log::warning("TopWear for men subcategory not found");
        }

        // Log the status
        if ($topWearWomen) {
            Log::info("Subcategory (Women) $topWearWomen->name", [
                "status" => (bool) $topWearWomen,
            ]);
        } else {
            Log::warning("TopWear for women subcategory not found");
        }

        // add product(s) for kevin
        if ($kevin && $topWearMen) {
            $kevinProduct = $kevin->products()->updateOrCreate(
                [
                    "name" => "Cotton Levi's Tshirt",
                ],
                [
                    "sub_category_id" => $topWearMen->id,
                    "description" => "Pure Cotton comfortable T-shirt",
                    "base_price" => 699.5,
                    "status" => "approved",
                ],
            );
            // log the status
            Log::info("$kevin->name added product", [
                "status" => (bool) $kevinProduct,
            ]);
        } else {
            // log the status
            Log::warning("Skipping the product addon for vendor Kevin");
        }

        // add product(s) for mayank
        if ($mayank && $topWearWomen) {
            $mayankProduct = $mayank->products()->updateOrCreate(
                [
                    "name" => "Sports Jacket for Women | Girls",
                ],
                [
                    "sub_category_id" => $topWearWomen->id,
                    "description" => "Suitable for workout, casuals etc.",
                    "base_price" => 399.0,
                    "status" => "pending",
                ],
            );
            // log the status
            Log::info("$mayank->name added product", [
                "status" => (bool) $mayankProduct,
            ]);
        } else {
            // log the status
            Log::warning("Skipping the product addon for vendor Mayank");
        }

        // log the end..
        Log::info("Product Data Seeding by Vendor ended!");
    }
}
