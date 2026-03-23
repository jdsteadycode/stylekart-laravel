<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Mail\DeliveryOtpMail;
use App\Models\Order;
use App\Notifications\Delivery\NewJobAvailableNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

        // get city of delivery person
        $city = DB::table('addresses')->where('user_id', $user->id)->value('city');

        // already accepted orders.
        $acceptedOrders = Order::where('delivery_person_id', $user->id)
            ->whereIn('order_status', ['shipped', 'out_for_delivery'])
            ->with(['user', 'address'])
            ->get();

        // Available Orders not assigned to anyone yet
        $availableOrders = collect();

        // show available orders if no order is taken by me etc.
        if ($deliveryProfile && $deliveryProfile->is_available && $acceptedOrders->isEmpty() && $city) {
            $availableOrders = Order::whereNull('delivery_person_id')
                ->where('order_status', 'ready_for_pickup')
                ->whereHas('items', function ($query) use ($city) {
                    // Match the item's vendor_id to users in the addresses table with the same city
                    $query->whereIn('vendor_id', function ($subQuery) use ($city) {
                        $subQuery->select('user_id')
                            ->from('addresses')
                            ->where('city', $city);
                    });
                })
                ->with(['user', 'address', 'items.vendor.vendorProfile', 'items.vendor.addresses'])
                ->get();
        }

        // new: added delivered orders
        $deliveredOrders = Order::where('delivery_person_id', $user->id)
            ->where('order_status', 'delivered')
            ->latest() // new first
            ->limit(20) // but only 20 not all
            ->get();

        return view(
            'delivery-person.dashboard.index',
            compact('availableOrders', 'acceptedOrders', 'deliveredOrders')
        );
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
        if (! $user->deliveryProfile->is_available) {
            return back()->with('error', 'You already have an active order.');
        }

        // wrap inside the transaction
        // DB::transaction(function () use ($order, $user) {
        //     // Assign order to me
        //     $order->update(['delivery_person_id' => $user->id]);

        //     // remove the notification as well
        //     DB::table('notifications')
        //         ->where('type', NewJobAvailableNotification::class)
        //         ->where('data->order_id', $order->id)
        //         ->delete();

        //     // Mark items as Shipped
        //     $order->items()->update(['order_status' => 'shipped']);

        //     // Sync main order status
        //     $order->updateStatus();

        //     // update the profile and mark as un-available
        //     $user->deliveryProfile()->update(['is_available' => false]);
        // });
        // start a manual transaction
        DB::beginTransaction();

        // safe execution
        try {
            // 🚀 get the order with a lock to prevent race conditions!
            // this ensures only one delivery person can process this order at a time
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            // check again inside the lock
            if ($lockedOrder->delivery_person_id !== null) {
                throw new \Exception('Too late! Someone else accepted this.');
            }

            // assign order to me
            $lockedOrder->update(['delivery_person_id' => $user->id]);

            // remove the notification as well
            DB::table('notifications')
                ->where('type', NewJobAvailableNotification::class)
                ->where('data->order_id', $order->id)
                ->delete();

            // mark items as shipped
            $lockedOrder->items()->update(['order_status' => 'shipped']);

            // sync main order status
            $lockedOrder->updateStatus();

            // update the profile and mark as un-available
            $user->deliveryProfile()->update(['is_available' => false]);

            // log the status
            logger()->info("Order #{$order->order_number} successfully claimed by {$user->name}");

            // save all changes!
            DB::commit();

            return redirect()->route('dashboard.delivery')
                ->with('success', 'Order accepted! Drive safe – your task is now active.');
        }

        // when SQL error happens
        catch (\Exception $e) {
            // undo the changes if any problem occurs
            DB::rollBack();

            // log the warning
            logger()->warning('Failed to accept order: '.$e->getMessage());

            return redirect()->route('dashboard.delivery')->with('error', $e->getMessage());
        }

        // show success message
        return redirect()->route('dashboard.delivery')
            ->with('success', 'Order accepted! Drive safe – your task is now active.');
    }

    /**
     * take action: complete
     */
    public function complete(Order $order, Request $request)
    {
        // log the info
        logger()->info("[app\Http\Controllers\Delivery\DashboardController@complete] Order action initiated");

        // get delivery person
        $user = auth()->user();

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
        if (! in_array($incomingStatus, $allowedTransitions[$currentStatus] ?? [])) {
            // log the status
            logger()->alert("Invalid Status {$currentStatus} >> {$incomingStatus} attempted to be transitioned");

            return back()->with('error', 'Invalid status jump attempted!');
        }

        // check if current status is shipped & new-status is out_for_delivery
        if ($currentStatus === 'shipped' && $incomingStatus === 'out_for_delivery') {

            // generate otp for customer
            $customerOtp = rand(100000, 999999);

            // update the order and save otp
            $otpSaved = $order->update(['delivery_otp' => $customerOtp]);

            // log the status
            logger()->info('Delivery Person marked order -> out-for-delivery & Otp generated!', [
                'status' => (bool) $otpSaved,
                'otp' => $customerOtp,
            ]);

            // send the mail to customer's email & with content
            $sent = Mail::to($order->user->email)->send(
                new DeliveryOtpMail(
                    otp: $customerOtp,
                    order: $order
                )
            );

            // log the status
            logger()->info('Customer is notified with Delivery Update', ['status' => (bool) $sent]);

            // process transaction
            return $this->processTransaction(
                order: $order,
                incomingStatus: $incomingStatus,
                message: 'You are on the way with order',
                lastStep: false
            );
        }

        // for final order delivery confirmation
        // i.e., current status => out_for_delivery && new status => delivered
        if ($currentStatus === 'out_for_delivery' && $incomingStatus === 'delivered') {

            // validate the otp from customer
            if (! $request->otp || $order->delivery_otp !== $request->otp) {
                // log the status
                logger()->alert('Invalid OTP! Please ask customer for valid otp');

                // back with error
                return back()->with('error', 'Invalid OTP! Please ask the customer for the correct 6-digit code.');
            }

            return $this->processTransaction(
                order: $order,
                incomingStatus: $incomingStatus,
                message: 'Order delivered successfully',
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
                logger()->info('Delivery Person is now free | Order was recently delivered');

                // if order was Cash On Delivery,
                if (strtolower($order->payment_mode) === 'cod') {

                    // update the status as paid,
                    $order->payment_status = 'paid';
                    $paid = $order->save();

                    // log the status
                    logger()->info("Order: #{$order->order_number} is now paid", ['status' => (bool) $paid]);
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
        return redirect()->back()->with('success', $message);
    }

    /**
     * show's order / job
     */
    public function showJob(Order $order)
    {
        // get current delivery person details..
        $user = auth()->user();

        // 1. Safety Check: If someone already took it, send them back
        if ($order->delivery_person_id !== null) {
            return redirect()->route('delivery.notifications.index')
                ->with('info', 'This job has already been accepted by another rider.');
        }

        // 2. NEW Safety Check: Am I already busy?
        if (! $user->deliveryProfile->is_available) {
            return redirect()->route('dashboard.delivery')
                ->with('error', 'You already have an active delivery. Complete it before viewing new jobs.');
        }

        // get all related data..
        $order->load(['user', 'address', 'items.vendor.vendorProfile', 'items.vendor.addresses']);

        return view('delivery-person.order.show', compact('order'));
    }
}
