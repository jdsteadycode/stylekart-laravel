<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
// use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Exception;

// get the OrderService Class path
use App\Services\OrderService;


class CheckoutController extends Controller
{
    /***
        intial checkout show
     */
    public function index()
    {

        // get the cart from session
        $bag = Session::get('bag', []);

        // when bag (cart) is empty
        if (count($bag) < 1) {
            // log the status..
            logger()->alert('No items exist in Bag');

            // redirect back to shop page..
            return redirect()->route('customer.shop')->with('error', 'Bag is empty. Nothing to checkout');
        }


        // log the action
        logger()->info('[app\Http\Controllers\Customer\CheckoutController@index] Data for Checkout initiated!');

        // check if user is authenticated customer?
        $customer = auth()->user();
        if (!$customer) {
            // log the status
            logger()->info('Customer is not logged-in. Checkout Data loading terminated');
            abort(403);
        }

        // get default address of customer (1 only for now)
        $address = $customer->addresses()->where('is_default', 1)->first();

        // if no default address found?
        if (!$address) {
            // no default address found
            logger()->warning("No default Address found! So fetching any!");

            // get first from any.
            $address = $customer->addresses()->first();

            // if no address found (from any)?
            if (!$address) {
                // no address found from customer
                logger()->alert("OOPS! customer doesn't seem to have any address");
            }
        } else {
            // log the status
            logger()->info("Address fetched for customer");
        }

        // calculate sub-total
        $subTotal = 0;
        // for each bag item
        foreach ($bag as $item) {
            // save total amount
            $subTotal += ($item['qty'] * $item['price']);
        }

        // get the view..
        return view('customer.checkout.index', compact('address', 'bag', 'subTotal'));
    }

    /***
        handle final checkout - place-order!
     */
    public function placeOrder(Request $request)
    {

        // log the action
        logger()->info('[app\Http\Controllers\Customer\CheckoutController@placeOrder] Order Placement initiated');

        // check if no customer
        $customer = auth()->user();
        if (!$customer) {
            //log the status
            logger()->alert('No customer authenticated found! Order Placement terminated.');
            abort(403);
        }

        // get the bag..
        $bag = Session::get('bag', []);

        // if bag is empty
        if (count($bag) < 1) {
            // log the status
            logger()->alert('Bag is empty! Order Placement terminated');
            abort(403);
        }

        // validate the incoming data..
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address_line' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'pay' => 'required|in:cod,online'
        ]);

        // log the validated and clean data
        logger()->info("Data validated!", ['data' => $validated]);

        // ensure safe execution.
        try {

            // instantiate the Service Class
            $orderService = new OrderService();
            $order = $orderService->createOrder($customer, $validated, $bag);   // try to make an order..

            // if cod!
            if ($validated['pay'] === 'cod') {

                // clear the cart! (bag)
                Session::forget('bag');

                // log the status..
                logger()->info('Bag emptied!');

                // send to order success page..
                return redirect()->route('customer.checkout.success', ['orderNumber' => $order->order_number]);
            }
            // otherwise, send to external gateway link
            else {
                return redirect()->route('customer.payment.mock', ['orderNumber' => $order->order_number]);
            }
        }

        // handle SQL errors
        catch (Exception $e) {

            // log the error
            logger()->error('Order Placement Failed', ['error' => $e->getMessage()]);

            // redirect to checkout fail page..
            return view('customer.checkout.failed');
        }
    }

    /***
     *
     * for order success page..
     */
    public function success($orderNumber)
    {

        // log the action
        logger()->info('[app\Http\Controllers\CheckoutController@success] order success view initiated!');

        // get the customer..
        $customer = auth()->user();
        if (!$customer) {
            // log the status
            logger()->alert('No authenticated customer');
            abort(403);
        }

        // get the order placed via orderNumber
        $order = $customer->orders()->where('order_number', $orderNumber)->first();

        // if no order
        if (!$order) {
            // log the status
            logger()->alert('No current order found! Terminating Order Success view');

            // redirect back
            return redirect()->route('customer.checkout')->with('error', "Can't checkout before order placement");
        }

        // send the view..
        return view('customer.checkout.success', compact('order'));
    }



    /*
    public function placeOrder(Request $request)
    {

        // log the action
        logger()->info('[app\Http\Controllers\Customer\CheckoutController@placeOrder] Order Placement initiated');

        // check if no customer
        $customer = auth()->user();
        if (!$customer) {
            //log the status
            logger()->alert('No customer authenticated found! Order Placement terminated.');
            abort(403);
        }

        // get the bag..
        $bag = Session::get('bag', []);

        // if bag is empty
        if (count($bag) < 1) {
            // log the status
            logger()->alert('Bag is empty! Order Placement terminated');
            abort(403);
        }

        // validate the incoming data..
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address_line' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'pay' => 'required|in:cod,online'
        ]);

        // log the validated and clean data
        logger()->info("Data validated!", ['data' => $validated]);

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

        // 2. Make an order
        // order number (unique / random)
        $orderNumber =
            "STK-" . now()->format("Ymd") . "-" . strtoupper(Str::random(6));

        // total amount
        $totalAmount = 0;

        // for each item in bag
        foreach ($bag as $item) {
            // get the variant from db..
            $variant = ProductVariant::find($item['variant_id']);

            // log the status
            logger()->info('variant fetched ', ['variant' => $variant]);

            // save it's total price..
            $totalAmount += $variant->price * $item['qty'];
        }

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
                return redirect()->back()->with('error', 'Insufficient Stock! for' . $variant->product->name);
            }

            // create order item
            $orderItem = $order->items()->create([
                'product_id' => $variant->product->id,
                'variant_id' => $variant->id,
                'vendor_id' => $variant->product->vendor->id,
                'quantity' => $item['qty'],
                'price' => $variant->price ?? $variant->product->base_price,
                'order_status' => 'pending',
                'payment_mode' => $request->pay,
                'payment_status' => 'pending' // cod
            ]);

            // reduce the stock according to qty
            // for that variant
            $variant->decrement('stock', $item['qty']);

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

        // 4. clear the cart!
        $removed = Session::remove('bag');

        // log the status..
        logger()->info('Bag emptied!', ['status' => (bool) $removed]);

        // send to order success page..
        return redirect()->route('customer.checkout.success', ['orderNumber' => $order->order_number]);
    }

    */
}
