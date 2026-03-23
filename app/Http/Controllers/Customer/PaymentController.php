<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\ProcessRequest;
use App\Mail\OrderSuccessMail;
use App\Notifications\Vendor\LowStockNotification;
use App\Services\MockPaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// path to MockPaymentService Class..
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

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
            if (! $variant) {
                // throw exception when SQL error..
                throw new Exception("Variant Not found for item {$item->id}");
            }

            // 1.
            // check stock
            if ($variant->stock < $item->quantity) {

                // log the status
                logger()->info($item->product->name." 's Stock is in-sufficient | Payment Failed");

                // throw error and go to catch block
                throw new Exception('Insufficent Stock for variant: '.$variant->id);
            }

            // log the status [before stock reduction]
            logger()->info('Checking variant: '.$variant->id.' stock: '.$variant->stock);

            // 2.
            // decrease the stock
            $variant->decrement('stock', $item->quantity);

            // log the status [after stock reduction | success]
            logger()->info('reduced the stock for variant: '.$variant->id.' stock: '.$variant->stock);

            // when stock is low of that variant
            if ($variant->stock <= 5) {
                // Notify the vendor of this specific product
                $variant->product->vendor->notify(new LowStockNotification($variant));

                // log the warning
                logger()->warning("[PaymentController] Low stock alert triggered for Variant: {$variant->id}");
            }

            // update the order status accordingly
            // $item->update([
            //     'order_status' => 'processing'  // if it was pending!
            // ]);
        }

        // 2. update order level payment details
        $order->update([
            // 'order_status' => 'processing',
            'payment_status' => 'paid',
        ]);
    }

    // () -> mock gateway view
    public function index($orderNumber)
    {

        // check if not authenticated
        $customer = auth()->user();
        if (! $customer) {
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
        if (! $order) {
            // log the status
            logger()->alert('No Order found for Customer: '.$customer->id);

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
        if (! $customer) {
            return redirect()->route('login');
        }

        // get the current order by customer..
        $order = $customer->orders()->where('order_number', $orderNumber)->where('payment_status', 'pending')->first();

        // check no order yet!
        if (! $order) {
            return redirect()->route('customer.checkout');
        }

        // log the action
        logger()->info('[app\Http\Controllers\Customer\PaymentController@process] Processing the payment');

        // validate the request input..
        $validated = $request->validated();

        // log the status
        logger()->info('Payment details validated', ['status' => (bool) $validated]);

        // MockPaymentService instantiation..
        $mockPaymentService = new MockPaymentService;

        // start a DB transaction..
        DB::beginTransaction();
        try {

            // call the MockPaymentGatewayService..
            $success = $mockPaymentService->charge($order, $validated);

            // if gateway payment response is not valid!
            if (! $success) {
                // throw the excecption..
                throw new Exception('MockPaymentService Payment Failed!');
            }

            // handle order updates..
            $this->confirmOrderPayment($order);

            // save the changes for DB
            DB::commit();

            // send mail
            Mail::to($customer->email)
                ->send(new OrderSuccessMail($order));

            // log the status
            logger()->info("Online Payment Invoice sent to: {$customer->email}");
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
            logger()->error('Payment failed | '.$e->getMessage());

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
