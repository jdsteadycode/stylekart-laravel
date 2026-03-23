<?php

// package path

namespace App\Services;

// grab the class paths..
use App\Mail\OrderSuccessMail;
// get the Exception Class Path
use App\Models\ProductVariant;
// get class path to Str
use App\Models\User;
// get DB Facade Class path
use App\Notifications\Vendor\LowStockNotification;
// get Mail Class Facade path
use App\Notifications\Vendor\NewOrderNotification;
// get class path to OrderSuccessMailable
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

// UDC Order Service
class OrderService
{
    /**
     * handle the order creation..
     */
    public function createOrder($customer, $address, array $validated, array $bag)
    {

        // log the action
        logger()->info('[app\Services\OrderService@createOrder] Order and Order Item creation initiated');

        // order number (unique / random)
        $orderNumber =
            'STK-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));

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
                if (! $variant) {
                    // log the error
                    throw new Exception("Product Variant not found for variant_id: {$item['variant_id']}");
                }

                // log the status
                logger()->info('variant fetched ', ['variant' => $variant]);

                // save it's total price..
                $totalAmount += $variant->selling_price * $item['qty'];
            }

            // 2. Make an order
            // create a new order..
            $order = $customer->orders()->create([
                'address_id' => $address->id,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'order_status' => 'pending',
                'payment_mode' => $validated['pay'],
                'payment_status' => 'pending', // for both cod or online (temp for online..),
            ]);

            // log the status..
            logger()->info('Order Created for customer '.$customer->name, ['status' => (bool) $order]);

            // 3. Save ordered items..
            foreach ($bag as $item) {

                // get the variant
                $variant = ProductVariant::where('id', $item['variant_id'])->lockForUpdate()->first();

                // when variant not found
                if (! $variant) {
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
                    throw new Exception('Insufficient stock for'.$variant->product->name);
                }

                // create order item
                $orderItem = $order->items()->create([
                    'product_id' => $variant->product->id,
                    'variant_id' => $variant->id,
                    'vendor_id' => $variant->product->vendor->id,
                    'quantity' => $item['qty'],
                    'price' => $variant->selling_price,
                    'order_status' => 'pending', // intially
                ]);

                // if payment method is cod only then,
                if ($validated['pay'] === 'cod') {
                    // reduce the stock according to qty
                    // for that variant
                    $variant->decrement('stock', $item['qty']);

                    // only when the variant is at low stock
                    if ($variant->stock <= 5) {
                        // get the vendor
                        $vendor = $variant->product->vendor;

                        // Notify the vendor immediately about this specific variant
                        $vendor->notify(new LowStockNotification($variant));

                        // log the warning.
                        logger()->warning("Low stock alert! Variant: {$variant->id} is at {$variant->stock}");
                    }
                }

                // log the status
                logger()->info(
                    'Order Item saved',
                    [
                        'status' => (bool) $orderItem,
                        'payment-method' => $order->payment_mode,
                        'payment-status' => $order->payment_status,
                    ]
                );
            }

            // commit changes..
            DB::commit();

            /***
             * notify the vendor
             */
            // get vendors who have their ordered items..
            $vendorIds = $order->items->pluck('vendor_id')->unique();

            // for each vendor
            foreach ($vendorIds as $vendorId) {
                $vendor = User::find($vendorId);

                if ($vendor) {
                    // send mail and notification as well
                    $vendor->notify(new NewOrderNotification($order));

                    // log the status
                    logger()->info("Order notification sent to Vendor: {$vendor->name} (ID: {$vendorId})");
                }
            }

            // send the mail to customer
            Mail::to($customer->email)
                ->send(new OrderSuccessMail($order));

            logger()->info("Online Payment Invoice sent to: {$customer->email}");

            // get the final order..
            return $order;
        }

        // when an SQL / DB problem occurs
        catch (Exception $e) {

            // undo the changes.. (i.e., if problem occurs in orderItem creation then, undo the Order creation keeping db in valid state etc,)
            DB::rollBack();

            // log the error
            logger()->error('Error: '.$e->getMessage());

            // throw the error
            throw $e;
        }
    }
}
