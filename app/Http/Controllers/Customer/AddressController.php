<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{

    /**
     * for creating a new address
     */
    public function create()
    {

        // log the action..
        logger()->info('[app\Http\Controllers\Customer\AddressController@create] Address Creation initiated!');

        // get the user..
        $customer = auth()->user();
        if (!$customer) {
            // log the status
            logger()->alert('Customer not authenticated! Terminating the New Address store');

            // redirect back to login..
            return redirect()->route('login');
        }

        // log the end..
        logger()->info('New Address view loaded!');

        // show the view..
        return view('customer.address.create');
    }

    // Store new address
    public function store(Request $request)
    {


        // log the action..
        logger()->info('[app\Http\Controllers\Customer\AddressController@store] New Address Save initiated!');

        // get the user..
        $customer = auth()->user();
        if (!$customer) {
            // log the status
            logger()->alert('Customer not authenticated! Terminating the New Address store');

            // redirect back to login..
            return redirect()->route('login');
        }

        // validate the data..
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'landmark' => 'nullable|string|max:100',
            'address_type' => 'required|in:home,office,other',
            'is_default' => 'nullable|boolean',
        ], [
            'address_type.exists' => 'Address Type is not valid!'
        ]);

        // log the status
        logger()->info('Data is validated!', ['status' => (bool) $validated]);

        // if this is to be default one, then reset previous default
        if ($request->has('is_default') && $request->is_default) {

            // log the status..
            logger()->warning('NOTE: Current New Address is to be default!');

            // reset the previous ones..
            $customer->addresses()->update(['is_default' => false]);
        }

        // create a new address for customer..
        $saved =  $customer->addresses()->create($validated);

        // log the status
        logger()->info('New Address Created!', ['status' => (bool) $saved]);

        // log the end..
        logger()->info('Address Store ended!');

        // redirect back to profile!
        return redirect()->route('customer.profile')->with('success', 'Address added successfully!');
    }

    // Show edit form
    public function edit(Address $address)
    {

        return view('customer.address.edit', compact('address'));
    }

    // Update existing address
    public function update(Request $request, Address $address)
    {

        // log the action..
        logger()->info('[app\Http\Controllers\Customer\AddressController@update] Existing Address Update initiated!');

        // get the user..
        $customer = auth()->user();
        if (!$customer) {
            // log the status
            logger()->alert('Customer not authenticated! Terminating the New Address store');

            // redirect back to login..
            return redirect()->route('login');
        }

        // validate the data..
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'address_type' => 'required|in:home,office,other',
            'landmark' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        // log the status.
        logger()->info('Data is validated!', ['status' => (bool) $validated]);

        // if this is to be default one, then reset previous default
        if ($request->has('is_default') && $request->is_default) {

            // log the status..
            logger()->warning('NOTE: Current Existing Address is to be default!');

            // reset the previous ones..
            $customer->addresses()->update(['is_default' => false]);
        }

        // update the address details
        $updated = $address->update([
            ...$validated,
            'is_default' => $request->get('is_default') ? true : false,
        ]);

        // log the status
        logger()->info('Address details updated!', ['status' => (bool) $updated]);

        // log the end
        logger()->info('Existing Address Update ended!');

        // send the customer back to profile with updated msg..
        return redirect()->route('customer.profile')->with('success', 'Address updated successfully!');
    }

    // Delete
    public function destroy(Address $address)
    {
        // log the action..
        logger()->info('[app\Http\Controllers\Customer\AddressController@destroy] Existing Address deletion initiated!');

        // get the user..
        $customer = auth()->user();
        if (!$customer) {
            // log the status
            logger()->alert('Customer not authenticated! Terminating the New Address store');

            // redirect back to login..
            return redirect()->route('login');
        }

        // delete the existing address..
        $deleted = $address->delete();

        // log the status..
        logger()->info('Existing Address deleted!', ['status' => (bool) $deleted]);

        // log the end.
        logger()->info('Address deletion complete.');

        // send back client with updated msg.
        return redirect()->route('customer.profile')->with('success', 'Address removed successfully!');
    }
}
