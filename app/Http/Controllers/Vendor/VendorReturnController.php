<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// get the OrderItem, DeliveryJob Model class path
use App\Models\OrderItem;
use App\Models\DeliveryJob;

// LogisticsJobsAvailable event
use App\Events\LogisticsJobAvailable;

class VendorReturnController extends Controller
{
    /**
     * all return order item requests
     */
    public function index()
    {

        // log the action
        logger()->info("[app\Http\Controllers\Vendor\VendorReturnController@index] All order item return requests requested!");

        // current vendor
        $vendor = auth()->user();

        // all return requested items..
        $returnRequests = $vendor->soldItems()
            ->whereNotNull('return_status')
            ->with(['order', 'product', 'variant'])
            ->orderBy('updated_at', 'desc')
            ->paginate(6);


        // send to the view
        return view('vendor.return.index', compact('returnRequests'));
    }

    /**
     * view the specific return item request?
     */
    public function show(OrderItem $item)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\VendorReturnController@show] Viewing return item request initiated!");

        // Security check: Make sure this vendor owns this item!
        if ($item->vendor_id !== auth()->user()->id) {

            // log the error
            logger()->error("Unauthorized access to wrong return request!");

            abort(403, 'Unauthorized access to this return request.');
        }

        // get the relationships
        $item->load(['order.user', 'product', 'variant', 'order.address']);

        // get the view and pass the item data along with relationships..
        return view('vendor.return.show', compact('item'));
    }

    /**
     * Approve the specific return item request
     */
    public function approve(Request $request, OrderItem $item)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\VendorReturnController@approve] Approve of item return request initiated");

        // Security Check
        if ($item->vendor_id !== auth()->user()->id) {
            // log the error
            logger()->error("Unauthorized access to wrong return request!");
            abort(403, 'Unauthorized access to this return request.');
        }

        // get the vendor
        $vendor = auth()->user();
        // vendor address
        $vendorAddress = $vendor->addresses()->first();
        // only city
        $vendorCity = $vendorAddress->city;

        // get the customer
        // $customer = $item->order->user;

        // get the customer address (during order time)
        $customerAddress = $item->order->address;

        // only city
        $customerCity = $customerAddress->city;

        // log the details (for test)
        logger()->info("City details!", [
            'customer city' => $customerCity,
            'vendor city' => $vendorCity,

            'customer address' => $customerAddress->address_line,
            'vendor address' => $vendorAddress->address_line
        ]);

        // for test
        // return;

        // Only allow approval if it's currently "requested"
        if ($item->return_status === 'requested') {


            /**
             * Create (Return) Job for delivery person(s)
             */
            $created = DeliveryJob::create([
                "job_type" => "return",
                "reference_id" => $item->id,

                // customer city
                "pickup_city" => $customerCity,

                // vendor city
                "dropoff_city" => $vendorCity,

                // customer address (detailed)
                "pickup_address" => [
                    'name' => $customerAddress->name,
                    'phone' => $customerAddress->phone,
                    'address_line' => $customerAddress->address_line,
                    'city' => $customerAddress->city,
                    'state' => $customerAddress->state,
                    'pincode' => $customerAddress->pincode,
                    'landmark' => $customerAddress->landmark,
                    'address_type' => $customerAddress->address_type,
                ],
                // vendor address (detailed)
                "dropoff_address" => [
                    'name' => $vendorAddress->name,
                    'phone' => $vendorAddress->phone,
                    'address_line' => $vendorAddress->address_line,
                    'city' => $vendorAddress->city,
                    'state' => $vendorAddress->state,
                    'pincode' => $vendorAddress->pincode,
                    'landmark' => $vendorAddress->landmark,
                    'address_type' => $vendorAddress->address_type,
                ],

                "status" => "available"     // for pickup!
            ]);

            // log the status
            logger()->info("Return Job Created!", [
                "status" => (bool) $created
            ]);

            // update the item request_status -> approved
            $item->update(['return_status' => 'approved']);

            // TODO for later: Fire an event here to notify the Delivery Gig Board!
            // trigger the LogisticsJobAvailable Event
            event(new LogisticsJobAvailable(
                job: $created,
                type: 'return',
                city: $customerCity,
                order: null
            ));

            // back with the success
            return back()->with('success', 'Return request approved. A delivery partner will be assigned.');
        }

        // otherwise redirect back with error
        return back()->with('error', 'Invalid return status.');
    }

    /**
     * reject the return request
     */
    public function reject(Request $request, OrderItem $item)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\VendorReturnController@reject] Rejection of item return request initiated");

        // Security Check
        if ($item->vendor_id !== auth()->user()->id) {
            // log the error
            logger()->error("Unauthorized access to wrong return request!");
            abort(403, 'Unauthorized access to this return request.');
        }

        // if current return request is requested?
        if ($item->return_status === 'requested') {
            // Optional MVP addition: You could validate a $request->reject_reason here if you want
            $item->update(['return_status' => 'rejected']);

            // back with success
            return back()->with('success', 'Return request rejected.');
        }

        // invalid return status
        return back()->with('error', 'Invalid return status.');
    }
}
