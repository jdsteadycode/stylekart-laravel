<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeliveryOtpMail;

class DashboardController extends Controller
{
    /**
     * for all orders assigned to delivery person
     */
    public function index()
    {
        // log the info
        logger()->info("[app\Http\Controllers\Delivery\DashboardController@index] All orders assigned view initiated");

        // delivery user & his/her's profile
        $user = auth()->user();
        $deliveryProfile = $user->deliveryProfile;

        // already accepted orders.
        $acceptedOrders = Order::where('delivery_person_id', $user->id)
            ->whereIn('order_status', ['shipped', 'out_for_delivery'])
            ->with(['user', 'address'])
            ->get();

        // Available Orders not assigned to anyone yet
        $availableOrders = collect();

        // show available orders if no order is taken by me etc.
        if ($deliveryProfile && $deliveryProfile->is_available && $acceptedOrders->isEmpty()) {
            $availableOrders = Order::whereNull('delivery_person_id')
                ->where('order_status', 'ready_for_pickup')
                ->with(['user', 'address'])
                ->get();
        }

        return view('delivery-person.dashboard.index', compact('availableOrders', 'acceptedOrders'));
    }

    /**
     * accept the order
     */
    public function accept(Order $order)
    {

        // log the action
        logger()->info("[app\Http\Controllers\Delivery\DashboardController@accept] Accepting Orders!");

        // get the delivery person
        $user = auth()->user();

        // avoid con-current clicks
        if ($order->delivery_person_id !== null) {
            return back()->with('error', 'Too late! Someone else accepted this.');
        }

        // Check if available or not
        if (!$user->deliveryProfile->is_available) {
            return back()->with('error', 'You already have an active order.');
        }

        // wrap inside the transaction
        DB::transaction(function () use ($order, $user) {
            // Assign order to me
            $order->update(['delivery_person_id' => $user->id]);

            // Mark items as Shipped
            $order->items()->update(['order_status' => 'shipped']);

            // Sync main order status
            $order->updateStatus();

            // update the profile and mark as un-available
            $user->deliveryProfile()->update(['is_available' => false]);
        });

        return back()->with('success', 'Order accepted! Your status is now BUSY.');
    }

    /**
     * take action: complete
     */
    public function complete(Order $order, Request $request)
    {
        // log the info
        logger()->info("[app\Http\Controllers\Delivery\DashboardController@complete] Order action initiated");

        // get delivery person
        $user =  auth()->user();

        // check if order was assigned to delivery
        if ($order->delivery_person_id !== $user->id) {
            abort(403);
        }

        //  Allowed Transitions
        $allowedTransitions = [
            'shipped' => ['out_for_delivery'],
            'out_for_delivery' => ['delivered'],
            'delivered' => [],
        ];

        // current status
        $currentStatus = $order->order_status;

        // incoming status
        $incomingStatus = $request->order_status;

        //  check if incoming status is transitioned correctly?
        if (!in_array($incomingStatus, $allowedTransitions[$currentStatus] ?? [])) {
            // log the status
            logger()->alert("Invalid Status {$currentStatus} >> {$incomingStatus} attempted to be transitioned");
            return back()->with('error', 'Invalid status jump attempted!');
        }

        // check if current status is shipped & new-status is out_for_delivery
        if ($currentStatus === "shipped" && $incomingStatus === "out_for_delivery") {

            // generate otp for customer
            $customerOtp = rand(100000, 999999);

            // update the order and save otp
            $otpSaved = $order->update(["delivery_otp" => $customerOtp]);

            // log the status
            logger()->info("Delivery Person marked order -> out-for-delivery & Otp generated!", [
                "status" => (bool) $otpSaved,
                "otp" => $customerOtp
            ]);

            // send the mail to customer's email & with content
            $sent = Mail::to($order->user->email)->send(
                new DeliveryOtpMail(
                    otp: $customerOtp,
                    order: $order
                )
            );

            // log the status
            logger()->info("Customer is notified with Delivery Update", ["status" => (bool) $sent]);

            // process transaction
            return $this->processTransaction(
                order: $order,
                incomingStatus: $incomingStatus,
                message: "You are on the way with order",
                lastStep: false
            );
        }

        // for final order delivery confirmation
        // i.e., current status => out_for_delivery && new status => delivered
        if ($currentStatus === "out_for_delivery" && $incomingStatus === "delivered") {

            // validate the otp from customer
            if (!$request->otp || $order->delivery_otp !== $request->otp) {
                // log the status
                logger()->alert("Invalid OTP! Please ask customer for valid otp");

                // back with error
                return back()->with('error', 'Invalid OTP! Please ask the customer for the correct 6-digit code.');
            }

            return $this->processTransaction(
                order: $order,
                incomingStatus: $incomingStatus,
                message: "Order delivered successfully",
                lastStep: true
            );
        }
    }

    /***
     * process order and order_item with transaction
     */
    public function processTransaction($order, $incomingStatus, $message, $lastStep = false)
    {
        // wrap inside transaction
        DB::transaction(function () use ($order, $incomingStatus, $lastStep) {

            // get delivery person
            $user = auth()->user();

            // update the items status
            $order->items()->update(['order_status' => $incomingStatus]);

            // sync with main order
            $order->updateStatus();

            // if delivery is on last step delivered!
            if ($lastStep && $incomingStatus === 'delivered') {

                // log the status
                logger()->info("Delivery Person is now free | Order was recently delivered");

                // if order was Cash On Delivery,
                if (strtolower($order->payment_mode) === 'cod') {

                    // update the status as paid,
                    $order->payment_status = 'paid';
                    $paid  = $order->save();

                    // log the status
                    logger()->info("Order: #{$order->order_number} is now paid", ["status" => (bool) $paid]);
                }

                // get the delivery profile
                $profile = $user->deliveryProfile;
                if ($profile) {

                    // make delivery person available
                    $profile->update(['is_available' => true]);
                }
            }
        });

        // back with success
        return redirect()->back()->with("success", $message);
    }
}
