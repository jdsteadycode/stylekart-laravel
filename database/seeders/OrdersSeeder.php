<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\CartItem;
use App\Models\Address;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // log the action
        logger()->info(
            "[database\seeders\OrdersSeeder@run] Seeding Orders data initiated",
        );

        // order number (unique / random)
        $orderNumber =
            "STK-" . now()->format("Ymd") . "-" . strtoupper(Str::random(6));

        // get customer
        $jinal = User::where("role", "customer")->first();

        // check
        if (!$jinal) {
            logger()->alert("OOPS! No customer found! Seeding terminated");
            return;
        }

        // get the addresses
        $homeAddress = $jinal->addresses()->first();

        // if no address
        if (!$homeAddress) {
            logger()->alert(
                "OOPS! No Address found for {$jinal->name}! Seeding terminated.",
            );
            return;
        }

        // get the cart items..
        $cartItems = $jinal->cartItems ?? collect();

        // check
        if ($cartItems->isEmpty()) {
            logger()->alert(
                "OOPS! No cart data found for {$jinal->name}! Seeding terminated.",
            );
            return;
        }

        // total amount
        $total_amount = 0;
        // iterate over items
        foreach ($cartItems as $item) {
            // get the price either from variant or main product's price
            $price = $item->variant->price ?? $item->product->base_price;
            $qty = $item->item_qty;

            // save the total for each item in cart
            $total_amount += $price * $qty;

            // Log the action
            logger()->info(
                "For Product: {$item->product->name}'s variant price: {$item->variant->price}, added quantity: {$item->item_qty} & total: {$total_amount}",
            );
        }

        // make the order.
        $orderCreated = $jinal->orders()->create([
            "order_number" => $orderNumber,
            "address_id" => $homeAddress->id,
            "total_amount" => $total_amount,
        ]);

        // log the action
        logger()->info("Order Created! #{$orderCreated->order_number}", [
            "status" => (bool) $orderCreated,
        ]);

        // log the end
        logger()->info("Orders Seeding complete!");
    }
}
