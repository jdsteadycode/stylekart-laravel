<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Log the action
        logger()->info(
            "[database\seeders\AddressesSeeder@run] Address Seeding initiated",
        );

        // get customer
        $jinal = User::where("role", "customer")->first();

        // check
        if (!$jinal) {
            logger()->alert("Customer not found, Seeding stopped");
            return;
        }

        // Add one / two addresses.
        $address1 = $jinal->addresses()->create([
            "name" => "Atharv",
            "phone" => "872354890",
            "address_line" => "Flat 02, Shanti housing Co-Op Soc.",
            "city" => "Bilimora",
            "state" => "Gujarat",
            "pincode" => "400100",
            "landmark" => "Near Reliance Trendz",
            "address_type" => "home",
            "is_default" => true,
        ]);

        // log the status
        logger()->info("$jinal->name added a new address for $address1->name", [
            "status" => (bool) $address1,
        ]);

        $address2 = $jinal->addresses()->create([
            "name" => "Jeet",
            "phone" => "3492882127",
            "address_line" => "Flat 03, Shraddha housing Co-Op Soc.",
            "city" => "Vapi",
            "state" => "Gujarat",
            "pincode" => "396191",
            "landmark" => "Near Bank of Baroda",
            "address_type" => "home",
            "is_default" => false,
        ]);

        // log the status
        logger()->info("$jinal->name added a new address for $address2->name", [
            "status" => (bool) $address2,
        ]);

        // Log the end
        logger()->info("Address Seeding complete!");
    }
}
