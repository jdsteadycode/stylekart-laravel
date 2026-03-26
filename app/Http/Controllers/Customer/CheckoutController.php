<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
// use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Requests\CheckoutRequest;

// get the OrderService Class path
use App\Services\OrderService;

// get class path to OrderPlacedEvent
use App\Events\OrderPlaced;


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

        // get all adresses saved by customer including default
        $addresses = $customer->addresses()->latest()->get();

        // if not found!
        if (!$addresses || $addresses->count() == 0) {
            // log the status
            logger()->alert("No saved addresses found");
        }

        // get default address or make one
        $defaultAddress = $addresses->where('is_default', 1)->first() ?? $addresses->first();

        // calculate sub-total
        $subTotal = 0;
        // for each bag item
        foreach ($bag as $item) {
            // save total amount
            $subTotal += ($item['qty'] * $item['price']);
        }

        // get the view..
        return view('customer.checkout.index', compact('addresses', 'defaultAddress', 'bag', 'subTotal'));
    }

    /***
        handle final checkout - place-order!
     */
    public function placeOrder(CheckoutRequest $request)
    {

        // log the action
        logger()->info('[app\Http\Controllers\Customer\CheckoutController@placeOrder] Order Placement initiated');

        // get the bag..
        $bag = Session::get('bag', []);

        // if bag is empty
        if (count($bag) < 1) {
            // log the status
            logger()->alert('Bag is empty! Order Placement terminated');
            abort(403);
        }

        // get validated request
        $validated = $request->validated();

        // log the validated and clean data
        logger()->info("Data validated!", ['data' => $validated]);

        // ensure safe execution.
        try {

            // get customer
            $customer = $request->user();


            // if default address opted?
            if ($request->filled('address_id')) {
                $address = $customer->addresses()->findOrFail($validated['address_id']);
                // log the status
                logger()->info("Opted for existing address", [
                    "status" => (bool) $address,
                    "data" => [
                        'id' => $address->id,
                        'name' => $address->name,
                    ]
                ]);
            }

            // asked to create a new address
            else {
                $address = $customer->addresses()->create([
                    'name'         => $validated['name'],
                    'phone'        => $validated['phone'],
                    'address_line' => $validated['address_line'],
                    'city'         => $validated['city'],
                    'pincode'      => $validated['pincode'],
                    'state'        => $validated['state'],
                    'address_type' => $validated['address_type']
                ]);

                // log the status
                logger()->info("New Address Added and Opted!", [
                    "status" => (bool) $address,
                    "address" => $address
                ]);
            }


            // test and check (temporary)
            // return;

            // instantiate the Service Class
            $orderService = new OrderService();
            $order = $orderService->createOrder($customer, $address, $validated, $bag);   // try to make an order..

            // if cod!
            if ($validated['pay'] === 'cod') {

                // clear the cart! (bag)
                Session::forget('bag');

                // log the status..
                logger()->info('Bag emptied!');

                // fire order placed event.
                event(new OrderPlaced($order));

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
            return view('customer.checkout.failed')->with('error', $e->getMessage());
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
}
