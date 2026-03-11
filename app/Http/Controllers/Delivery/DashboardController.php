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

        // Get orders assigned to the logged-in delivery person
        $orders = Order::where('delivery_person_id', auth()->user()->id)
            ->whereIn('order_status', ['shipped', 'out_for_delivery'])
            ->with(['user', 'address'])
            ->get();

        return view('delivery-person.dashboard.index', compact('orders'));
    }

    /**
     * take action: complete
     */
    public function complete(Order $order, Request $request)
    {
        // log the info
        logger()->info("[app\Http\Controllers\Delivery\DashboardController@complete] Order action initiated");

        // check if order was assigned to delivery
        if ($order->delivery_person_id !== auth()->user()->id) {
            abort(403);
        }

        //  Allowed Transitions (The Guard)
        $allowedTransitions = [
            'shipped' => ['out_for_delivery'],
            'out_for_delivery' => ['delivered'],
            'delivered' => [], // End of the road
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
            return $this->processTransaction($order, $incomingStatus, "You are on the way with order");
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

            return $this->processTransaction($order, $incomingStatus, "Order delivered successfully");
        }
    }

    /***
     * process order and order_item with transaction
     */
    public function processTransaction($order, $incomingStatus, $message)
    {
        // wrap inside transaction
        DB::transaction(function () use ($order, $incomingStatus) {
            // update the items status
            $order->items()->update(['order_status' => $incomingStatus]);

            // sync with main order
            $order->updateStatus();
        });

        // back with success
        return redirect()->back()->with("success", $message);
    }
}
