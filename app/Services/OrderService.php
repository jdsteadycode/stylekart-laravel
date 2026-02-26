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

// UDC Order Service
class OrderService
{

    /**
     * handle the order creation..
     */
    public function createOrder($customer, array $validated, array $bag)
    {


        // log the action
        logger()->info('[app\Services\OrderService@createOrder] Order and Order Item creation initiated');

        // get default address if exists
        $address = $customer->addresses()->where('is_default', 1)->first();

        // if default address
        if ($address) {
            // update existing default address
            $address->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address_line' => $validated['address_line'],
                'city' => $validated['city'],
                'pincode' => $validated['pincode'],
                'state' => $address->state ?? 'Gujarat',
                'landmark' => $address->landmark ?? 'Near Reliance Trendz',
                'address_type' => $address->address_type ?? 'home',
            ]);

            // log the status
            logger()->info("Default address updated!", ['address_id' => $address->id]);
        } else {
            // create new default address
            $address = $customer->addresses()->create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address_line' => $validated['address_line'],
                'city' => $validated['city'],
                'pincode' => $validated['pincode'],
                'state' => 'Gujarat',
                'landmark' => 'Near Reliance Trendz',
                'address_type' => 'home',
                'is_default' => 1,
            ]);

            // log the status
            logger()->info("New default address created!", ['address_id' => $address->id]);
        }


        // order number (unique / random)
        $orderNumber =
            "STK-" . now()->format("Ymd") . "-" . strtoupper(Str::random(6));

        // total amount
        $totalAmount = 0;

        // for online payment
        // check if existing order is there
        $existingOrder = $customer->orders()
            ->whereHas('items', function ($subQuery) {
                $subQuery->where('payment_mode', 'online')
                    ->where('payment_status', 'pending');
            })->first();
        if ($existingOrder) {

            // log the status
            logger()->info("Existing Upaid Order [Online] | Redirected to gateway");

            // get the existing order..
            return $existingOrder;
        }


        // start a new transaction
        DB::beginTransaction();

        // ensure safe execution.
        try {

            // for each item in bag
            foreach ($bag as $item) {
                // get the variant from db..
                $variant = ProductVariant::find($item['variant_id']);

                // log the status
                logger()->info('variant fetched ', ['variant' => $variant]);

                // save it's total price..
                $totalAmount += $variant->price * $item['qty'];
            }

            // 2. Make an order
            // create a new order..
            $order = $customer->orders()->create([
                'address_id' => $address->id,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
            ]);

            // log the status..
            logger()->info('Order Created for customer ' . $customer->name, ['status' => (bool) $order]);

            // 3. Save ordered items..
            foreach ($bag as $item) {

                // get the variant
                $variant = ProductVariant::find($item['variant_id']);

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
                    'price' => $variant->price ?? $variant->product->base_price,
                    'order_status' => 'pending',
                    'payment_mode' => $validated['pay'] ?? 'cod',
                    'payment_status' => 'pending' // for both cod or online (temp for online..)
                ]);


                // if payment method is cod only then,
                if ($validated['pay'] === 'cod') {
                    // reduce the stock according to qty
                    // for that variant
                    $variant->decrement('stock', $item['qty']);
                }

                // log the status
                logger()->info(
                    'Order Item saved',
                    [
                        'status' => (bool) $orderItem,
                        'payment-method' => $orderItem->payment_mode,
                        'payment-status' => $orderItem->payment_status
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
