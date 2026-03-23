<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DeliveryPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // log the info
        logger()->info("[database\seeders\DeliveryPersonSeeder@run] Seeding Delivery Person user data");

        // delivery persons (for users)
        $persons = [
            [
                'name' => 'Suresh Delivery',
                'email' => 'suresh@stylekart.com',
                'vehicle_number' => 'GJ-05-AB-1234',
            ],
            [
                'name' => 'Mahesh Delivery',
                'email' => 'mahesh@stylekart.com',
                'vehicle_number' => 'GJ-05-CD-5678',
            ],
        ];

        // for each delivery person
        foreach ($persons as $person) {
            // create them as user
            $user = User::create([
                'name' => $person['name'],
                'email' => $person['email'],
                'password' => bcrypt('password'),
                'role' => 'delivery_person',
            ]);

            // make their profile
            $user->deliveryProfile()->create([
                'vehicle_number' => $person['vehicle_number'],
                'vehicle_type' => 'Bike',
                'phone_number' => '9876543210',
                'is_available' => true,
            ]);
        }

        // log the end
        logger()->info('Delivery Person Seeding done');
    }
}
