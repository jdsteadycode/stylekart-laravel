<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Session;

// path to MockPaymentService Class..
use App\Services\MockPaymentService;

class PaymentController extends Controller
{
    /**
     *  handle payment confirmation..
     */
    public function confirmOrderPayment($order)
    {

        // iterate each order item
        foreach ($order->items as $item) {

            // get the variant
            $variant = $item->variant()->lockForUpdate()->first();

            // 1.
            // check stock
            if ($variant->stock < $item->quantity) {

                // log the status
                logger()->info($item->product->name . " 's Stock is in-sufficient | Payment Failed");

                // throw error and go to catch block
                throw new Exception('Insufficent Stock for variant: ' . $variant->id);
            }

            // log the status [before stock reduction]
            logger()->info('Checking variant: ' . $variant->id . ' stock: ' . $variant->stock);


            // 2.
            // decrease the stock
            $variant->decrement('stock', $item->quantity);

            // log the status [after stock reduction | success]
            logger()->info('reduced the stock for variant: ' . $variant->id . ' stock: ' . $variant->stock);

            // update the payment status accordingly
            $item->update([
                'payment_status' => 'paid'
            ]);
        }
    }

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

        // check no order yet!
        if (!$order) return redirect()->route('customer.checkout');

        // check if order was already paid!
        if ($order->items()->where('payment_status', '!=', 'paid')->count() === 0) {

            // log the status
            logger()->alert('It looks like Order: ' . $orderNumber . ' was already paid!');

            // redirect back to success page..
            return redirect()->route('customer.checkout.success', [
                'orderNumber' => $orderNumber
            ]);
        }


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
        // $paymentStatus = true;

        // MockPaymentService instantiation..
        $mockPaymentService = new MockPaymentService();

        // start a DB transaction..
        DB::beginTransaction();
        try {

            // call the MockPaymentGatewayService..
            $success = $mockPaymentService->charge($order, $validated);

            // if gateway payment response is not valid!
            if (!$success) {
                // throw the excecption..
                throw new Exception("MockPaymentService Payment Failed!");
            }

            // handle order updates..
            $this->confirmOrderPayment($order);

            // save the changes for DB
            DB::commit();
        }

        // handle SQL errors
        catch (Exception $e) {

            // undo the changes if any (like: order_items update etc..)
            DB::rollBack();

            // update the payment status
            // $paymentStatus = false;

            // log the status
            logger()->error('Payment failed | ' . $e->getMessage());

            // redirect back to order-failed view
            return view('customer.checkout.failed')->with('error', $e->getMessage());
        }

        // get the bag / cart
        // $bag = Session::get('bag', []);

        // clear the bag
        Session::forget('bag');

        // log the action
        logger()->info('bag cleared');

        return redirect()->route('customer.checkout.success', ['orderNumber' => $orderNumber]);
    }
}
