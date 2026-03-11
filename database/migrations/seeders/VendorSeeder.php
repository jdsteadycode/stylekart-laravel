<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

use App\Models\VendorProfile;
use App\Models\User;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Log action
        Log::info("[VendorSeeder@run] Seeding Vendor Profile data");

        // Create Vendor User
        $vendor = User::updateOrCreate(
            [
                "email" => "kevin@gmail.com",
            ],
            [
                "name" => "kevin",
                "email" => "kevin@gmail.com",
                "password" => Hash::make("kevin123"),
                "role" => "vendor",
            ],
        );
        // log the status
        Log::info("Saved " . $vendor->name . " as vendor.", [
            "status" => (bool) $vendor,
        ]);

        // Create Vendor Profile for Kevin
        $vendorProfile = VendorProfile::updateOrCreate(
            [
                "user_id" => $vendor->id,
            ],
            [
                "shop_name" => "StyleKart Fashion Hub Branch (2)",
                "shop_address" => "123 Market Street",
                "phone" => "9876543210",
                "status" => "approved",
            ],
        );
        // log the status
        Log::info("Created " . $vendor->name . "finalized profile", [
            "status" => (bool) $vendorProfile,
        ]);

        // Create vendor profile for mayank
        $mayank = User::where("email", "mayank@gmail.com")->first();
        if ($mayank) {
            $mayankProfile = VendorProfile::updateOrCreate(
                [
                    "user_id" => $mayank->id,
                ],
                [
                    "shop_name" => "StyleKart Fashion Hub Branch (3)",
                    "shop_address" => "Pine Street",
                    "phone" => "4528782903",
                    "status" => "pending",
                ],
            );

            // log the status
            Log::info($mayank->name . " finalized profile", [
                "status" => (bool) $mayankProfile,
            ]);
        } else {
            Log::warning("Mayank user not found. Skipping profile creation.");
        }
        // Log end
        Log::info("VendorProfile Seeding done!");
    }
}
