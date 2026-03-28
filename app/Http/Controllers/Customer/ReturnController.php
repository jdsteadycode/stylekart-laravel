<?php

// package path
namespace App\Http\Controllers\Customer;

// Controller class path
use App\Http\Controllers\Controller;

// Request class path
use Illuminate\Http\Request;

// ReturnRequest class path
use App\Http\Requests\Customer\ReturnRequest;

// OrderItem Model class path
use App\Models\OrderItem;

// UDC - ReturnController inheriting Controller class
class ReturnController extends Controller
{
    /**
     * recieve order item return request
     */
    public function store(ReturnRequest $request, OrderItem $item) {

        // log the status
        logger()->info("[app\Http\Controllers\Customer\ReturnController@store] Order Item return initiated!");

        /**
         * checks
         */
        // ensure customer is returning own delivered item!!
        if($item->order->user_id !== auth()->user()->id) {

            // log the status
            logger()->info("Cannot access the other's order and delivered item");

            // abort the access
            abort(403);
        }

        // ensure the ordered item belonging to customer is delivered!
        if($item->order_status !== 'delivered') {

            // log the status
            logger()->info("Cannot return the item as it's not delivered!");

            // abort
            abort(404);
        }

        // ensure the ordered item is not already returned!
        if($item->return_status !== null) {

            // log the status
            logger()->info("Cannot return the item which is already returned!");

            // abort
            abort(404);
        }


        // further return process...
        // log the validated data..
        logger()->info("Validation was successfull | Proceeding to further return process");

        // get the validated data
        $validated = $request->validated();

        // log the validated data
        // logger()->info($validated);
        // return;

        // order item return status changed to requested!
        $item->update([
            'return_status' => 'requested',
            'return_reason' => $validated['reason']
        ]);


        // log the status
        logger()->info("Return request stored for item {$item->id}");

        // back with success
        return back()->with('success', 'Return request submitted successfully!');
    }
}
