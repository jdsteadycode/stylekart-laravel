<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Vendor\VendorOrderRequest;

class VendorOrderController extends Controller
{

    /**
     * Display vendor incoming orders
     */
    public function index(Request $request)
    {
        // log the action..
        logger()->info('[app\Http\Controllers\Vendor\VendorOrderController@index] All Vendor related orders requested!');

        // get the vendor..
        $vendor = auth()->user();

        // check vendor?
        if (!$vendor) {

            // log the status
            logger()->alert('No authenticated Vendor');
            abort(403);
        }

        // get order status..
        $status = $request->query('status');

        // log the stats..
        logger()->info('Vendor order items fetched', [
            'status' => $status ?? 'all'
        ]);

        // get order items of vendor
        $query = Order::whereHas('items', function ($query) use ($vendor, $status) {
            $query->where('vendor_id', $vendor->id);

            // if status?
            if ($status) {

                // filter by status
                $query->where('order_status', $status);
            }
        })
            ->with(['items' => fn($query) => $query->where('vendor_id', $vendor->id)])
            ->latest();

        // get the orders..
        $orders = $query->paginate(10)->withQueryString();

        // pass on to index view..
        return view('vendor.orders.index', compact('orders'));
    }

    /**
     * Show specific order (only vendor items)
     */
    public function show(Order $order)
    {
        // log the action..
        logger()->info('[app\Http\Controllers\Vendor\VendorOrderController@show] Order detail view requested!');

        // get the vendor..
        $vendor = auth()->user();

        // check vendor?
        if (!$vendor) {

            // log the status
            logger()->alert('No authenticated Vendor');

            // block access
            abort(403);
        }

        // Ensure this vendor owns at least one item
        if (!$order->items()->where('vendor_id', $vendor->id)->exists()) {

            // log the status
            logger()->alert('The order does not contain any vendor items! Terminated order detail view..');

            // block access
            abort(403);
        }

        // load related data..
        $order->load([
            'items' => function ($query) use ($vendor) {
                // get order items but, related to vendor..
                $query->where('vendor_id', $vendor->id);
            },
            // get personal info
            'user',
            // get address info
            'address'
        ]);

        // send to orders detail view..
        return view('vendor.orders.show', compact('order'));
    }

    /**
     * Update vendor order item status
     */
    public function updateStatus(VendorOrderRequest $request, OrderItem $item)
    {

        // log the action..
        logger()->info('[app\Http\Controllers\Vendor\VendorOrderController@updateStatus] Vendor Ordered item status update initiated.');

        // get vendor details..
        $vendor = auth()->user();

        $validated = $request->validated();

        // check if ordered item / whole order was cancelled!
        if ($item->order_status === 'cancelled' || $item->order->order_status === 'cancelled') {

            // log the status
            logger()->alert('Order or ordered Item was already cancelled!');

            // back with error
            return back()->with('error', 'Ordered item or order was already cancelled!');
        }


        // set the transitions for preventing to invalid order state..
        $allowedTransitions = [
            'pending' => ['processing'],
            'processing' => ['ready_for_pickup'],
            'ready_for_pickup' => [],
            'shipped' => [],
            'delivered' => [],
        ];

        // current status
        $currentStatus = $item->order_status;

        // incoming status
        $incomingStatus = $validated['order_status'];

        // when status is same
        if ($incomingStatus === $currentStatus) {
            // log the status
            logger()->alert("Invalid status transition attempted: {$currentStatus} → {$incomingStatus}");

            // back with info
            return back()->with('info', 'Status already updated.');
        }

        // check if invalid transition requested!
        if (!in_array($incomingStatus, $allowedTransitions[$currentStatus] ?? [])) {

            // log the status
            logger()->alert("Invalid status transition attempted: {$currentStatus} → {$incomingStatus}");

            // back with error..
            return back()->with('error', 'Invalid status transition.');
        }


        // start a transaction to avoid invalid db states!
        // i.e., item status updated but later order level status update fails!
        // which means one place data correct, other place in-correct!
        // So transaction ensures both happen or none..
        DB::transaction(function () use ($item, $validated) {

            // log the status
            logger()->info('before status update!');

            // update the status..
            $item->update([
                'order_status' => $validated['order_status']
            ]);

            // Sync parent order status
            $updated = $item->order->updateStatus();

            // log status
            logger()->info(
                'Updated the order status',
                [
                    'status' => (bool) $updated,
                    'new' => $item->order->order_status
                ]
            );
        });

        // redirect back with success
        return back()->with('success', 'Order status updated successfully.');
    }

    /**
     *
     *  cancel incoming order item..
     */
    public function cancel(OrderItem $item, VendorOrderRequest $request)
    {
        // log the action
        logger()->info('[VendorOrderController@cancel] Vendor cancellation initiated.');


        // wrap in transaction for valid db state..
        DB::transaction(function () use ($item, $request) {

            // Update item status
            $item->update([
                'order_status' => 'cancelled',
                'cancel_reason' => $request->cancel_reason
            ]);

            // Restore stock
            if ($item->variant) {
                $item->variant->increment('stock', $item->quantity);
            }

            // Sync parent order status
            $item->order->updateStatus();

            logger()->info('Vendor order item cancelled successfully.');
        });

        return back()->with('success', 'Order item cancelled successfully.');
    }
}
