<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * show order details..
     */
    public function show(Order $order)
    {
        // log the action
        logger()->info('[app\Http\Controllers\Customer\OrderController@show] Customer Order view initiated!');

        // get the actual user..
        $customer = auth()->user();

        // if not authenticated!
        if (!$customer) {

            // log the status
            logger()->info('Customer not authenticated! Terminating Order View');

            // redirect back to login..
            return redirect()->route('login');
        }

        // Prevent accessing someone else's order
        if ($order->user_id !== $customer->id) {

            // log the status
            logger()->alert('Order does not belong to the customer!');

            // go 403 error page..
            abort(403);
        }

        // get the ordered product & variant
        $order->load(['items.product', 'items.variant']);

        // send to customer view..
        return view('customer.orders.show', compact('order'));
    }

    /**
     * Cancel individual ordered item
     */
    public function cancelItem($orderNumber, OrderItem $item, Request $request)
    {

        // log the action
        logger()->info('[app\Http\Controllers\Customer\OrderController@cancelItem] Ordered Item cancellation initiated!');

        // get customer
        $customer = auth()->user();
        if (!$customer) {
            // log the status
            logger()->alert('Customer not authenticated');

            return redirect()->route('login');
        }

        // get the order..
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {

            // log the status
            logger()->alert('No Order found!');

            // redirect back with error
            return redirect()->back()->with('error', 'no order exists!');
        }

        // check if order belongs to customer!
        if ($customer->id !== $order->user_id) {
            // log the status
            logger()->info('Order does not belong to the customer Cancellation aborted.');
            abort(403);
        }

        // check if ordered item belongs to recent order!
        if ($order->id !== $item->order_id) {
            // log the status
            logger()->info('Item does not belong to current order! Cancellation aborted.');
            abort(403);
        }

        // Check if item can be cancelled
        if (in_array($item->order_status, ['shipped', 'delivered'])) {
            return back()->with('error', 'This item cannot be cancelled.');
        }

        // wrap it in transaction to ensure db is valid state..
        DB::transaction(function () use ($item, $request, $order) {
            // 1. Update item status
            $item->update([
                'order_status' => 'cancelled',
                'cancel_reason' => $request->cancel_reason ?? null,
            ]);

            // check if order was successful!
            if ($order->wasStockReduced()) {
                //. 2. Restore stock
                $restored = $item->variant->increment('stock', $item->quantity);

                // log the status
                logger()->info("Stock Restored! Variant id: $item->variant_id | stock: {$item->variant->stock}", ['status' => (bool) $restored]);
            }

            // log the status..
            logger()->info('Order status synced!');

            // 3. sync state with order. (update order level status if needed)
            $order->updateStatus();

            // log the status..
            logger()->info('Order status synced!');

            // log the status.
            logger()->info("Order Item #{$item->id} cancelled by customer.");
        });

        return back()->with('success', 'Item cancelled successfully.');
    }

    /***
     * full order cancel
     */
    public function cancelFullOrder($orderNumber)
    {

        // log the action
        logger()->info('[app\Http\Controllers\Customer\OrderController@cancelFullOrder] Full Order cancellation initiated!');

        // get customer
        $customer = auth()->user();
        if (!$customer) {
            // log the status
            logger()->alert('Customer not authenticated');

            return redirect()->route('login');
        }

        // get the order..
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $customer->id)
            ->first();

        // no order exist?
        if (!$order) {

            // log the status
            logger()->alert('No Order found!');

            // redirect back with error
            return redirect()->back()->with('error', 'no order exists!');
        }

        // Allow cancel only in pending or processing
        if (!in_array($order->order_status, ['pending', 'processing'])) {

            // log the status
            logger()->info('Order cannot be cancelled because it is out for delivery or delivered.');

            // back with error.
            return back()->with('error', 'Order cannot be cancelled.');
        }

        // wrap it in transaction
        DB::transaction(function () use ($order) {

            // for all items
            foreach ($order->items as $item) {

                // Only restore if not already cancelled
                if ($item->order_status !== 'cancelled') {

                    // log the status
                    logger()->info('Variant ' . $item->variant_id . "'s" . ' Stock before: ' . $item->variant->stock);

                    // check during online payment or cod! which means stock was reduced before!
                    if ($order->wasStockReduced()) {

                        // restore the stock
                        $restored = $item->variant->increment('stock', $item->quantity);

                        // log the status
                        logger()->info("Stock Restored! Variant id: $item->variant_id | stock: {$item->variant->stock}", ['status' => (bool) $restored]);
                    }


                    // Update item
                    $updated = $item->update([
                        'order_status' => 'cancelled',
                        'cancel_reason' => 'full order cancelled'
                    ]);

                    // log the status
                    logger()->info('Item cancelled! Variant id: ' . $item->variant_id, ['status' => (bool) $updated]);
                }
            }

            // log the statues
            logger()->info('All ordered items cancelled');

            // Cancel order itself
            $order->update([
                'order_status' => 'cancelled'
            ]);

            // log the status.
            logger()->info('Order state updated!');

            // sync the state according to its items ordered.
            $order->updateStatus();

            // log the status
            logger()->info('Order order-status state synced!');
        });

        // back with success.
        return back()->with('success', 'Order cancelled successfully.');
    }

    /**
     * show invoice
     */
    public function showInvoice($orderId)
    {
        $order = Order::with('items.variant.product', 'user')->findOrFail($orderId);

        return view('customer.orders.invoice', compact('order'));
    }
}
