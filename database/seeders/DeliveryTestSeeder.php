<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class DeliveryTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // log the action
        logger()->info("[database\seeders\DeliveryTestSeeder@run] Seeding Delivery Test Data");

        // get the admin, customer, vendor details..
        $customer_id = 3;

        // make an order
        $order = Order::create([
            'user_id' => $customer_id,
            'order_number' => 'STK-TEST-'.time(),
            'address_id' => 3,
            'total_amount' => 1600,
            'order_status' => 'pending',
            'payment_mode' => 'cod',
            'payment_status' => 'pending',
        ]);

        // make order items
        // from vendor 6
        $orderedItem1 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => 4,
            'variant_id' => 9,
            'vendor_id' => 6,
            'quantity' => 1,
            'price' => 399.00,
            'order_status' => 'ready_for_pickup',
        ]);
        // log the status
        logger()->info('Order items created from vendor 6', ['status' => (bool) $orderedItem1]);

        // order items from vendor 7
        $orderedItem2 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => 7,
            'variant_id' => 13,
            'vendor_id' => 7,
            'quantity' => 1,
            'price' => 1299.00,
            'order_status' => 'ready_for_pickup',
        ]);

        // log the status
        logger()->info('Order items created from vendor 7', ['status' => (bool) $orderedItem2]);

        // log the end
        logger()->info('Delivery Seeding terminated');
    }
}
