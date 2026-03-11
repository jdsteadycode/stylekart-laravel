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
