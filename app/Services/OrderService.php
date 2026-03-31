<?php

// package path
namespace App\Services;

// grab the class paths..
use App\Models\ProductVariant;

// get the Exception Class Path
use Exception;

// get class path to Str
use Illuminate\Support\Str;

// get DB Facade Class path
use Illuminate\Support\Facades\DB;

// get Mail Class Facade path
use Illuminate\Support\Facades\Mail;

// get class path to OrderSuccessMailable
// use App\Mail\OrderSuccessMail;

// get class path to OrderPlacedEvent, LowVariantStockReached
// use App\Events\OrderPlaced;
use App\Events\LowVariantStockReached;

// UDC Order Service
class OrderService
{

    /**
     * handle the order creation..
     */
    public function createOrder($customer, $address, array $validated, array $bag, $useWallet = false)
    {


        // log the action
        logger()->info('[app\Services\OrderService@createOrder] Order and Order Item creation initiated');

        // order number (unique / random)
        $orderNumber =
            "STK-" . now()->format("Ymd") . "-" . strtoupper(Str::random(6));

        // total amount
        $totalAmount = 0;

        // for online payment
        // check if existing order is there and remove them
        $deletedRows = $customer->orders()
            ->where('payment_mode', 'online')
            ->where('payment_status', 'pending')
            ->delete();
        // log the status
        logger()->alert('Removed unpaid online orders', ['total' => $deletedRows]);


        // start a new transaction
        DB::beginTransaction();

        // ensure safe execution.
        try {

            // for each item in bag
            foreach ($bag as $item) {
                // get the variant from db..
                $variant = ProductVariant::find($item['variant_id']);

                // when variant not found
                if (!$variant) {
                    // log the error
                    throw new Exception("Product Variant not found for variant_id: {$item['variant_id']}");
                }

                // log the status
                logger()->info('variant fetched ', ['variant' => $variant]);

                // save it's total price..
                $totalAmount += $variant->selling_price * $item['qty'];
            }

            // initial walletAmount
            $walletAmountUsed = 0;
            $payableAmount = $totalAmount;

            // if wallet was used by customer
            if ($useWallet && $customer->wallet) {

                // wallet balance
                $walletBalance  = $customer->wallet->balance ?? 0;

                // get the amount which smaller either wallet's balance or total amount
                $walletAmountUsed = min($totalAmount, $walletBalance);

                // payable amount calculation
                $payableAmount = $totalAmount - $walletAmountUsed;
            }

            // 2. Make an order
            // create a new order..
            $order = $customer->orders()->create([
                'address_id' => $address->id,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'wallet_amount_used' => $walletAmountUsed,
                'payable_amount'     => $payableAmount,
                'order_status' => 'pending',
                'payment_mode' => $payableAmount <= 0 ? 'wallet' : ($validated['pay'] ?? null),
                'payment_status' =>  $payableAmount <= 0 ? 'paid' : 'pending', // for both cod or online (temp for online..),
            ]);

            // log the status..
            logger()->info('Order Created for customer ' . $customer->name, ['status' => (bool) $order]);

            // if wallet amount is used.
            if ($walletAmountUsed > 0) {

                // log the amount before deduction
                logger()->info("Wallet amount before deduction: {$customer->wallet->balance}");

                // deduct the amount from wallet
                $customer->wallet->decrement('balance', $walletAmountUsed);

                // log the amount after deduction
                logger()->info("Wallet amount after deduction: {$customer->wallet->balance}");

                // make a transaction entry
                $customer->wallet->transactions()->create([
                    'type' => 'debit',
                    'amount' => $walletAmountUsed,
                    'description' => "Order Payment #$orderNumber",
                    'reference_type' => get_class($order),
                    'reference_id' => $order->id
                ]);
            }

            // 3. Save ordered items..
            foreach ($bag as $item) {

                // get the variant
                $variant = ProductVariant::where('id', $item['variant_id'])->lockForUpdate()->first();

                // when variant not found
                if (!$variant) {
                    // log the error
                    throw new Exception("Product Variant not found for variant_id: {$item['variant_id']}");
                }

                // stock before ordered..
                logger()->info('Stock before Order!', ['stock' => $variant->stock]);

                // check if stock is available
                if ($variant->stock < $item['qty']) {
                    // log the status
                    logger()->alert('Insufficient stock', [
                        'variant_id' => $variant->id,
                        'stock' => $variant->stock,
                        'quantity' => $item['qty'],
                    ]);

                    // redirect back with error
                    // go to catch part
                    throw new Exception('Insufficient stock for' . $variant->product->name);
                }

                // create order item
                $orderItem = $order->items()->create([
                    'product_id' => $variant->product->id,
                    'variant_id' => $variant->id,
                    'vendor_id' => $variant->product->vendor->id,
                    'quantity' => $item['qty'],
                    'price' => $variant->selling_price,
                    'order_status' => 'pending' // intially
                ]);


                // if payment method is cod only or wallet paid the full price then,
                if ($payableAmount <= 0 || $validated['pay'] === 'cod') {

                    // deduct the stock
                    deductVariantStock($variant, $item['qty']);

                    // only when the variant is at low stock
                    if ($variant->stock <= 5) {
                        // low variant event.
                        event(new LowVariantStockReached($variant));
                    }
                }

                // log the status
                logger()->info(
                    'Order Item saved',
                    [
                        'status' => (bool) $orderItem,
                        'payment-method' => $order->payment_mode,
                        'payment-status' => $order->payment_status
                    ]
                );
            }

            // commit changes..
            DB::commit();

            // get the final order..
            return $order;
        }

        // when an SQL / DB problem occurs
        catch (Exception $e) {

            // undo the changes.. (i.e., if problem occurs in orderItem creation then, undo the Order creation keeping db in valid state etc,)
            DB::rollBack();

            // log the error
            logger()->error('Error: ' . $e->getMessage());

            // throw the error
            throw $e;
        }
    }
}
