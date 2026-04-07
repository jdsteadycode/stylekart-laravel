<?php

// folder path
namespace App\Observers;

// Model class path(s)
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wallet;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        // log the action.
        logger()->info("[app\Observers\OrderItemObserver@updated] Observed an update in OrderItem!");

        // check if order-item's order_status was changed also, if changed order_status is 'delivered'?
        if ($orderItem->wasChanged('order_status') && $orderItem->order_status === 'delivered') {

            // To prevent the duplication, check if commission was already calculated??
            if ($orderItem->admin_commission !== null) {
                // log the warning
                logger()->warning("[app\Observers\OrderItemObserver@updated] Commission already processed for Item ID: {$orderItem->id}. Skipping.");
                return;
            }

            // Math for commission evaluation.
            // Commission type for admin i.e., fixed percentage (10%)
            $totalItemPrice = $orderItem->price * $orderItem->quantity;
            $adminCommission = $totalItemPrice * 0.10;

            // Commission (90%) for vendor is difference from admin and total item's worth amount
            // is vendor's commission (earning)
            $vendorEarning = $totalItemPrice - $adminCommission;

            // Modify and save the data..
            // Note: We use updateQuietly() to prevent firing the 'updated' observer again, causing an infinite loop!
            $orderItem->updateQuietly([
                'admin_commission' => $adminCommission,
                'vendor_earning' => $vendorEarning
            ]);

            // Log the status
            logger()->info("[app\Observers\OrderItemObserver@updated] Item {$orderItem->id} Delivered. Price: ₹{$totalItemPrice}. Admin Cut: ₹{$adminCommission}. Vendor Keeps (Earning): ₹{$vendorEarning}.");

            /**
             * WALLET DISTRIBUTION
             */

            // 1. Process Admin Commission
            // Find the first user with the role 'admin'
            $admin = User::where('role', 'admin')->first();

            // if admin user exists?
            if ($admin) {

                // create one wallet or get the existing one.
                $adminWallet = Wallet::firstOrCreate(
                    ['user_id' => $admin->id],
                    ['balance' => 0.00]
                );

                // add (increment or credit) the amount to admin's wallet as part of commission.
                $adminWallet->increment('balance', $adminCommission);

                // also, record this as a transaction (10%) of commission
                $adminWallet->transactions()->create([
                    'type' => 'credit',
                    'amount' => $adminCommission,
                    'description' => "Commission for Order #{$orderItem->order->order_number} (Item ID: {$orderItem->id})",
                    'reference_type' => get_class($orderItem),
                    'reference_id' => $orderItem->id,
                ]);
            }

            // otherwise if no admin found!
            else {
                // log the critical message
                logger()->critical("[app\Observers\OrderItemObserver@updated] CRITICAL: No Admin user found to receive commission!");
            }

            // 2. Process Vendor Earnings
            // if vendor exists?
            if ($orderItem->vendor_id) {

                // get the existing wallet or make one.
                $vendorWallet = Wallet::firstOrCreate(
                    ['user_id' => $orderItem->vendor_id],
                    ['balance' => 0.00]
                );

                // increment the earnings to the balance (credit)
                $vendorWallet->increment('balance', $vendorEarning);

                // record this transaction (90%) of remaining amount
                $vendorWallet->transactions()->create([
                    'type' => 'credit',
                    'amount' => $vendorEarning,
                    'description' => "Earnings for Order #{$orderItem->order->order_number} (Item ID: {$orderItem->id})",
                    'reference_type' => get_class($orderItem),
                    'reference_id' => $orderItem->id,
                ]);
            }

            // Final Debug Log
            logger()->info("[app\Observers\OrderItemObserver@updated] Wallet distribution complete for Item ID: {$orderItem->id}");
        }
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "restored" event.
     */
    public function restored(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     */
    public function forceDeleted(OrderItem $orderItem): void
    {
        //
    }
}
