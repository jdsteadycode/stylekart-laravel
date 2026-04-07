<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// get the Model class paths
use App\Models\DeliveryJob;
use App\Models\OrderItem;
use App\Models\Wallet;

// Exceptionclass path
use Exception;

// DB Facade class path
use Illuminate\Support\Facades\DB;

// RefundProcessed Event class path
use App\Events\RefundProcessed;


class ReturnJobController extends Controller
{

    /**
     * all return job orders
     */
    public function index()
    {

        // log the action
        logger()->info("[app\Http\Controllers\Delivery\ReturnJobController@index] All return jobs initiated!");

        $deliveryPerson = auth()->user();

        // Get driver's city
        $city = $deliveryPerson->addresses()->first()->city;

        // Available returns in their city
        $availableReturns = DeliveryJob::where('status', 'available')
            ->where('pickup_city', $city)
            ->get();

        // Their active return (if any)
        $activeReturn = DeliveryJob::where('delivery_person_id', $deliveryPerson->id)
            ->whereIn('status', ['accepted', 'picked_up'])
            ->first();

        // already returned / completed - return orders.. (history)
        $completedReturns = DeliveryJob::where('delivery_person_id', $deliveryPerson->id)
            ->where('status', 'completed')
            ->latest()
            ->limit(20)
            ->get();

        // send data to view..
        return view('delivery-person.return.index', compact(
            'availableReturns',
            'activeReturn',
            'completedReturns'
        ));
    }

    /**
     * Show the specific Return Job details
     */
    public function showJob(DeliveryJob $job)
    {

        // log the action
        logger()->info("[app\Http\Controllers\Delivery\ReturnJobController@showJob] Viewing current return job!");

        // get the delivery person
        $deliveryPerson = auth()->user();

        // when the job is already picked up by another delivery person
        if ($job->delivery_person_id !== null && $job->delivery_person_id !== $deliveryPerson->id) {
            return redirect()->route('dashboard.delivery')
                ->with('info', 'This return pickup has already been accepted by another rider.');
        }

        // when delivery person already has a job to do
        if (!$deliveryPerson->deliveryProfile->is_available && $job->delivery_person_id !== $deliveryPerson->id) {
            return redirect()->route('dashboard.delivery')
                ->with('error', 'You already have an active task. Complete it before viewing new jobs.');
        }

        return view('delivery-person.return.show', compact('job'));
    }

    /**
     * Accept the Return Job
     */
    public function accept(DeliveryJob $job)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Delivery\ReturnJobController@accept] Accepting Return Pickup!");

        // current logged-in delivery person
        $deliveryPerson = auth()->user();

        // a transaction for consistent and safer updates
        DB::beginTransaction();

        // try safely
        try {
            // lock the job for current action
            $lockedJob = DeliveryJob::where('id', $job->id)->lockForUpdate()->first();

            // if delivery person recently picked up the job
            if ($lockedJob->delivery_person_id !== null) {
                throw new \Exception('Too late! Someone else accepted this pickup.');
            }

            // Update the job
            $lockedJob->update([
                'delivery_person_id' => $deliveryPerson->id,
                'status' => 'accepted'
            ]);

            // make the current delivery person un-available / busy
            $deliveryPerson->deliveryProfile()->update(['is_available' => false]);

            // save the changes and record the changes..
            DB::commit();

            // trigger the ReturnJobAccepted Event (for notifying customer)
            event(new \App\Events\ReturnJobAccepted($lockedJob));

            // sucess and redirect
            return redirect()->route('delivery.return.index')
                ->with('success', 'Return pickup accepted! Head to the customer\'s location.');
        }

        // catch the run-time SQL errors..
        catch (\Exception $e) {

            // rollback if any update above goes wrong
            DB::rollBack();

            // log the status
            logger()->warning("Failed to accept return job: " . $e->getMessage());

            // redirect back with error
            return redirect()->route('delivery.return.index')->with('error', $e->getMessage());
        }
    }

    /**
     * handle the refund process
     */
    public function refundAmountToCustomer($orderItem, $refundAmount)
    {

        // Either get the existing customer's wallet or make one..
        $customerWallet = Wallet::firstOrCreate(
            ['user_id' => $orderItem->order->user_id],
            ['balance' => 0.00]
        );

        // Add the money back to customer's wallet (for refund)
        $customerWallet->increment('balance', $refundAmount);

        // Make an entry for history and track record
        $customerWallet->transactions()->create([
            'type' => 'credit',
            'amount' => $refundAmount,
            'description' => "Refund for returned item (Order #" . $orderItem->order->order_number . ")",

            // Ensure link to OrderItem Model class path
            'reference_type' => get_class($orderItem),
            'reference_id' => $orderItem->id,
        ]);

        // log the status
        logger()->info("Refund Processed: ₹{$refundAmount} credited to User ID {$orderItem->order->user_id}");

        // old
        // trigger refund processed event!
        // event(new RefundProcessed($orderItem, $refundAmount));
    }

    /**
     * Update the status (Accepted -> Picked Up -> Completed)
     */
    public function complete(DeliveryJob $job, Request $request)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Delivery\ReturnJobController@complete] Completion of Pickup Job initiated!");

        // get current authenticated delivery person
        $deliveryPerson = auth()->user();

        // check if current job doesn't belong to current delivery person
        if ($job->delivery_person_id !== $deliveryPerson->id) {

            // log the status
            logger()->info("Un-authorized access | Current job doesn't belong to you.");

            // abort
            abort(403);
        }

        $incomingStatus = $request->status; // Expecting 'picked_up' or 'completed'

        // initial orderItem, refund amount
        $orderItem = null;
        $refundAmount = 0;

        // begin the transaction
        DB::beginTransaction();

        try {
            // Step 1: Rider picked up the item from the Customer
            if ($job->status === 'accepted' && $incomingStatus === 'picked_up') {

                // mark the job as picked up (from customer side)
                $job->update(['status' => 'picked_up']);
                $message = "Item picked up successfully! Now deliver it to the vendor.";
            }

            // if current item is given to vendor already
            elseif ($job->status === 'picked_up' && $incomingStatus === 'completed') {

                // mark job as completed (dropped the item at vendor side)
                $job->update(['status' => 'completed']);

                // get the order item?
                $orderItem = OrderItem::find($job->reference_id);

                // if order item found..
                if ($orderItem) {
                    // update the orderItem's return-status as recived as vendor recieved the item
                    $orderItem->update(['return_status' => 'received']);

                    // log the status
                    logger()->info("Before re-stock: {$orderItem->variant->stock}");

                    // ensure variant is incremented as well
                    // quantity
                    $quantity = $orderItem->quantity ?? 0;
                    $orderItem->variant->increment('stock', $quantity);

                    // log the status
                    logger()->info("After re-stock: {$orderItem->variant->stock}");

                    // 💰 THE REFUND LOGIC 💰
                    $refundAmount = $orderItem->price * $orderItem->quantity;

                    // refund here (or ensure refund begins when vendor approves the return request)
                    // new: added refund part..
                    $this->refundAmountToCustomer($orderItem, $refundAmount);

                    /*
                    * return fee
                    */
                    $vendorId = $orderItem->vendor_id;
                    $returnFee = 15.00;

                    // get existing or make one wallet for delivery person
                    $deliveryWallet = Wallet::firstOrCreate(
                        ['user_id' => $deliveryPerson->id],
                        ['balance' => 0.00]
                    );

                    // add amount to wallet
                    $deliveryWallet->increment('balance', $returnFee);

                    // record this transaction
                    $deliveryWallet->transactions()->create([
                        'type' => 'credit',
                        'amount' => $returnFee,
                        'description' => "Return Delivery Payout for Order #" . $orderItem->order->order_number,
                        'reference_type' => get_class($job),
                        'reference_id' => $job->id,
                    ]);

                    // get or create the vendor wallet
                    $vendorWallet = Wallet::firstOrCreate(
                        ['user_id' => $vendorId],
                        ['balance' => 0.00]
                    );

                    // update the vendor's balance amount
                    $vendorWallet->decrement('balance', $orderItem->vendor_earning);

                    // record the transaction as well
                    $vendorWallet->transactions()->create([
                        'type' => 'debit',
                        'amount' => $orderItem->vendor_earning,
                        'description' => "Earnings Reversal (Refunded) for Item ID: {$orderItem->id}",
                        'reference_type' => get_class($orderItem),
                        'reference_id' => $orderItem->id,
                    ]);

                    // B. Charge the ₹15 Return Fee
                    $vendorWallet->decrement('balance', $returnFee);

                    // record this transaction
                    $vendorWallet->transactions()->create([
                        'type' => 'debit',
                        'amount' => $returnFee,
                        'description' => "Return Fee Deduction for Order #" . $orderItem->order->order_number,
                        'reference_type' => get_class($job),
                        'reference_id' => $job->id,
                    ]);

                    // Log the status
                    logger()->info("[ReturnJobController@complete] Vendor {$vendorId} debited ₹{$orderItem->vendor_earning} (reversal) + ₹15 (return fee). Delivery person credited ₹15.");
                }

                // update the delivery person status as free
                $deliveryPerson->deliveryProfile()->update(['is_available' => true]);

                // message of delivery return completion
                $message = "Return delivery completed successfully! Great job.";
            }

            // otherwise, throw sql error.
            else {
                throw new \Exception('Invalid status transition.');
            }

            // save the changes and push to db
            DB::commit();

            // only trigger event if delivery person has handed the item to vendor
            if ($incomingStatus === 'completed' && isset($orderItem)) {
                // trigger the refund customer event
                event(new RefundProcessed($orderItem, $refundAmount));
            }

            // back with success
            return back()->with('success', $message);
        }

        // catch the sql run-time error
        catch (\Exception $e) {

            // rollback
            DB::rollBack();

            // back with the error message
            return back()->with('error', $e->getMessage());
        }
    }
}
