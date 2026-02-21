<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;

class OrderItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // log the actions..
        logger()->info(
            "[database/seeders/OrderItemsSeeder@run] Seeding Order Items data initiated!",
        );

        // get the cart of the customer
        $jinal = User::where("role", "customer")->first();

        // check
        if (!$jinal) {
            logger()->alert("OOPS! No customer found. Seeding terminated.");
            return;
        }

        // get the cart data..
        $cartItems = $jinal->cartItems ?? collect();

        // check
        if ($cartItems->isEmpty()) {
            logger()->alert(
                "OOPS! {$jinal->name} has no cart data. Seeding terminated",
            );
            return;
        }

        // get the order details
        $orderDetails = Order::where("user_id", $jinal->id)->first();

        // check
        if (!$orderDetails) {
            logger()->alert("OOPS! {$jinal->name} doesn't have any orders!");
            return;
        }

        // iterate over the cart items..
        foreach ($cartItems as $item) {
            // stock before
            logger()->info(
                "BEFORE ORDER! {$item->product->name}'s variant: {$item->variant->size}'s stock: {$item->variant->stock}",
            );

            // check stock availability
            if ($item->variant->stock < $item->item_qty) {
                logger()->alert(
                    "Caution! Product: {$item->product->name}'s Variant has low stock. Couldn't complete order. Seeding terminated",
                );
                return;
            }

            // save the order items..
            $itemOrdered = $orderDetails->items()->updateOrCreate(
                [
                    "product_id" => $item->product_id,
                    "variant_id" => $item->variant_id,
                    "vendor_id" => $item->product->vendor_id,
                ],
                [
                    "quantity" => $item->item_qty,
                    "price" =>
                        $item->variant->price ?? $item->product->base_price,
                    "order_status" => "pending",
                    "payment_mode" => "cod",
                    "payment_status" => "pending",
                ],
            );

            // log the action.
            logger()->info(
                "Item {$item->product->name} | order-status: {$itemOrdered->order_status} | payment-mode: {$itemOrdered->payment_mode} | payment-status: {$itemOrdered->payment_status}",
                [
                    "status" => (bool) $itemOrdered,
                ],
            );

            // decrease stock
            $updatedStock = $item->variant->decrement("stock", $item->item_qty);
            $item->variant->refresh(); // refresh the data

            // Log the action
            logger()->warning(
                "STOCK AFTER ORDER: {$item->product->name}'s Variant: {$item->variant->size}'s Stock: {$item->variant->stock}",
                ["status" => (bool) $updatedStock],
            );
        }

        // delete the data from cart..
        $cartDataDeleted = $jinal->cartItems()->delete();

        // log the action..
        logger()->info("{$jinal->name}'s Cart data deleted!", [
            "status" => (bool) $cartDataDeleted,
        ]);

        // Log the end
        logger()->info("Order Items seeding complete!");
    }
}
