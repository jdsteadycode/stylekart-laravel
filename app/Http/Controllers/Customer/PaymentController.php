<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\ProcessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Session;
use App\Mail\OrderSuccessMail;
use Illuminate\Support\Facades\Mail;

// path to MockPaymentService Class..
use App\Services\MockPaymentService;

// path to OrderPlaced Event, LowVariantStockReached Event class
use App\Events\LowVariantStockReached;
use App\Events\OrderPlaced;

class PaymentController extends Controller
{
    /**
     *  handle payment confirmation..
     */
    public function confirmOrderPayment($order)
    {

        // 1. handle stock
        // iterate each order item
        foreach ($order->items as $item) {

            // get the variant
            $variant = $item->variant()->lockForUpdate()->first();

            // check
            if (!$variant) {
                // throw exception when SQL error..
                throw new Exception("Variant Not found for item {$item->id}");
            }

            // deduct the stock
            deductVariantStock($variant, $item->quantity);

            // when stock is low of that variant
            if ($variant->stock <= 5) {
                // low variant event.
                event(new LowVariantStockReached($variant));
            }

            // update the order status accordingly
            // $item->update([
            //     'order_status' => 'processing'  // if it was pending!
            // ]);
        }

        // 2. update order level payment details
        $order->update([
            // 'order_status' => 'processing',
            'payment_status' => 'paid'
        ]);
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
        $order = $customer->orders()
            ->where('order_number', $orderNumber)
            ->where('payment_status', 'pending')->first();

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
    public function process($orderNumber, ProcessRequest $request)
    {

        // get the customer authenticated..
        $customer = auth()->user();
        if (!$customer) return redirect()->route('login');

        // get the current order by customer..
        $order = $customer->orders()->where('order_number', $orderNumber)->where('payment_status', 'pending')->first();

        // check no order yet!
        if (!$order) return redirect()->route('customer.checkout');


        // log the action
        logger()->info('[app\Http\Controllers\Customer\PaymentController@process] Processing the payment');

        // validate the request input..
        $validated = $request->validated();

        // log the status
        logger()->info('Payment details validated', ['status' => (bool) $validated]);

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

            // fire the Order placed event.
            event(new OrderPlaced($order));
        }

        // handle SQL errors
        catch (Exception $e) {

            // undo the changes if any (like: order_items update etc..)
            DB::rollBack();

            // update the payment status
            // $paymentStatus = false;
            $order->update([
                'payment_status' => 'failed',
            ]);

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
