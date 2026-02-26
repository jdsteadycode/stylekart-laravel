<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    // () -> mock gateway view
    public function index($orderNumber)
    {

        // check if not authenticated
        $customer = auth()->user();
        if (!$customer) {
            // log the action
            logger()->alert('Custmer not authenticated');

            // redirect back to login
            return redirect()->route('login');
        }

        // get the current order
        $order = $customer->orders()->where('order_number', $orderNumber)->first();

        // when no current order found
        if (!$order) {
            // log the status
            logger()->alert('No Order found for Customer: ' . $customer->id);

            // redirect back to checkout
            return redirect()->route('customer.checkout');
        }

        // log the action
        logger()->info('[app\Http\Controllers\Customer\PaymentController@index] redirection to mock gateway initiated.');

        // redirect to gateway view
        return view('customer.payment.mock', ['order' => $order]);
    }

    // () -> process payment
    public function process($orderNumber, Request $request)
    {

        // get the customer authenticated..
        $customer = auth()->user();
        if (!$customer) return redirect()->route('login');

        // get the current order by customer..
        $order = $customer->orders()->where('order_number', $orderNumber)->first();
        if (!$order) return redirect()->route('customer.checkout');

        // log the action
        logger()->info('[app\Http\Controllers\Customer\PaymentController@process] Processing the payment');

        // validate the request input..
        $validated = $request->validate([
            'card_number' => 'required|string',
            'expiry' => 'required|string',
            'cvv' => 'required|string',
            'card_name' => 'required|string'
        ]);

        // log the status
        logger()->info('Payment details verified', ['status' => (bool) $validated]);

        // state of payment
        $paymentStatus = true;

        // start a DB transaction..
        DB::beginTransaction();
        try {

            // iterate each order item
            foreach ($order->items as $item) {

                // update the payment status accordingly
                $item->update([
                    'payment_status' => $paymentStatus ? 'paid' : 'failed'
                ]);

                // check if stock is valid
                if ($paymentStatus) {
                    // get the variant
                    $variant = $item->variant;

                    // check stock
                    if ($variant->stock < $item->quantity) {

                        // log the status
                        logger()->info($item->product->name . " 's Stock is in-sufficient | Payment Failed");

                        // throw error and go to catch block
                        throw new Exception('Insufficent Stock');
                    }

                    // decrease the stock
                    $variant->decrement('stock', $item->quantity);

                    // log the status
                    logger()->info('reduced the stock for variant: ' . $variant->id . ' stock: ' . $variant->stock);
                }
            }

            // save the changes for DB
            DB::commit();

            // update the payment status
            $paymentStatus = true;
        }

        // handle SQL errors
        catch (Exception $e) {

            // undo the changes if any (like: order_items update etc..)
            DB::rollBack();

            // update the payment status
            $paymentStatus = false;

            // log the status
            logger()->error('Payment failed | ' . $e->getMessage());

            // redirect back to order-failed view
            return redirect()->route('customer.checkout.failed')->with('error', $e->getMessage());
        }



        // send the customer to order success / fail according to payment state
        if ($paymentStatus) {

            // get the bag / cart
            $bag = Session::get('bag', []);

            // clear the bag
            Session::forget('bag');

            // log the action
            logger()->info('bag cleared');

            return redirect()->route('customer.checkout.success', ['orderNumber' => $orderNumber]);
        } else {
            return redirect()->route('customer.checkout.failed')->with('error', 'Payment failed!');
        }
    }
}
