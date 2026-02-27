<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    /**
     * handle avatar color
     */
    public function getAvatarColor(string $letter)
    {

        // a color array..
        $colors = [
            'A' => '#F43F5E', // rose
            'B' => '#3B82F6', // blue
            'C' => '#10B981', // green
            'D' => '#F59E0B', // yellow
            'E' => '#8B5CF6', // purple
            'F' => '#EC4899', // pink

            // fallback for all other letters
            'default' => '#F97316' // orange
        ];

        // get the color or default one..
        return $colors[$letter] ?? $colors['default'];
    }

    /**
     * customer's profile..
     */
    public function index()
    {

        // get the current customer..
        $customer = auth()->user();
        if (!$customer) {
            // log the status
            logger()->alert('Customer not authenticated! | Being redirected to Login View');

            // send user to login..
            return redirect()->route('login');
        }

        // log the action..
        logger()->info('[app\Http\Controllers\Customer\ProfileController@index] Customer Profile requested!');

        // get the customer addresses
        $addresses = $customer->addresses()->orderBy('created_at', 'desc')->get();
        if (!$addresses->count()) {
            // log the status
            logger()->info('No addresses found for customer');
        }

        // get the recent orders.. (total: 5)
        $recentOrders = $customer->orders()->limit(5)->orderBy('updated_at', 'desc')->get();
        if (!$recentOrders->count()) {
            // log the status
            logger()->info('No orders found for customer');
        }

        // log the status
        logger()->info('Fetched addresses saved by customer!', ['total' => $addresses->count()]);

        // for avatar etc.
        // get the first letter of customer's name..
        $firstLetter = strtoupper(substr($customer->name, 0, 1));
        $avatarColor = $this->getAvatarColor($firstLetter);

        // log the status
        logger()->info('Profile for Customer prepared and fetch complete!');

        // send to view..
        return view('customer.profile.index', [
            'customer' => $customer,
            'addresses' => $addresses,
            'recentOrders' => $recentOrders,
            'recentOrdersCount' => $recentOrders->count(),
            'firstLetter' => $firstLetter,
            'avatarColor' => $avatarColor
        ]);
    }

    /**
     * update the existing profile
     */
    public function update(Request $request)
    {
        // get the current customer
        $customer = auth()->user();
        // if no customer!
        if (!$customer) {
            // log the status
            logger()->alert('Customer not authenticated! | Being redirected to Login View');

            // send user to login.
            return redirect()->route('login');
        }

        // log the action..
        logger()->info('[app\Http\Controllers\Customer\ProfileController@update] Profile update initiated!');

        // validate input
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255'
        ]);

        // update customer
        $updated = $customer->update($data);

        // log the status
        logger()->info('Customer profile updated successfully', ['status' => (bool) $updated]);

        // log the end.
        logger()->info('Customer profile update ended!');

        return redirect()->route('customer.profile')->with('success', 'Profile updated successfully!');
    }
}
