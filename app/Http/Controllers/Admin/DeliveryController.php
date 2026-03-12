<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Http\Requests\Admin\DeliveryRequest;
// use App\Models\User;
use App\Models\Order;

class DeliveryController extends Controller
{
    /**
     * get orders to be delivered yet
     */
    public function index()
    {
        // Get orders that don't have a delivery person yet
        $orders = Order::whereNull('delivery_person_id')
            ->get()
            // but to be handed over to delivery person
            ->filter(function ($order) {
                return $order->isConsolidated();
            });

        // Get all users who are delivery personnel for the dropdown
        // $deliveryPersons = User::where('role', 'delivery_person')->get();

        // get the view..
        return view('admin.deliveries.index', compact('orders'));
    }

    /**
     * assign the order to delivery person
     */
    public function assign(DeliveryRequest $request)
    {
        // log the status
        logger()->info("[app\Http\Controllers\Admin\DeliveryController@assign] Delivery person assign initiated");

        // Find the Order
        $order = Order::findOrFail($request->order_id);

        // assign the delivery person
        $order->update([
            'delivery_person_id' => $request->delivery_person_id,
        ]);

        // Update all items in this order to 'shipped' as well
        $order->items()->update(['order_status' => 'shipped']);

        // sync with main order status
        $order->updateStatus();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Delivery person assigned successfully!');
    }
}
