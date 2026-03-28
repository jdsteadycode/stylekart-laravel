<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// get the Model class paths
use App\Models\DeliveryJob;
use App\Models\OrderItem;

// DB Facade class path
use Illuminate\Support\Facades\DB;


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

        return view('delivery-person.return.index', compact('availableReturns', 'activeReturn'));
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
        logger()->info("[app\Http\Controllers\Delivery\ReturnJobController@accept] Accepting Return Pickup!");
        $deliveryPerson = auth()->user();

        DB::beginTransaction();

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

            // sucess and redirect
            return redirect()->route('delivery.return.index')
                ->with('success', 'Return pickup accepted! Head to the customer\'s location.');
        } catch (\Exception $e) {

            // rollback if any update above goes wrong
            DB::rollBack();

            // log the status
            logger()->warning("Failed to accept return job: " . $e->getMessage());

            // redirect back with error
            return redirect()->route('delivery.return.index')->with('error', $e->getMessage());
        }
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

                // update the order item's return status
                $orderItem = OrderItem::find($job->reference_id);
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

                    // refund here (or ensure refund begins when vendor approves the return request)
                }

                // update the delivery person status as free
                $deliveryPerson->deliveryProfile()->update(['is_available' => true]);

                // message of delivery return completion
                $message = "Return delivery completed successfully! Great job.";
            }

            // otherwise throw sql error.
            else {
                throw new \Exception('Invalid status transition.');
            }

            // save the changes and push to db
            DB::commit();

            // back with success
            return back()->with('success', $message);
        } catch (\Exception $e) {

            // rollback
            DB::rollBack();

            // back with the error message
            return back()->with('error', $e->getMessage());
        }
    }
}
