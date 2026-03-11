<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    /**
     * all wishlisted items
     */
    public function index()
    {
        // log the action
        logger()->info("[app\Http\Controllers\Customer\WishlistController@index] All wishlist items requested!");

        // get wishlist items for authenticated customer
        $wishlistedItems = auth()->user()
            ->wishlist()
            ->with('product.variants', 'variant')
            ->latest()
            ->get();

        // log the status
        logger()->info("Items in wishlist requested!", ['total' => (bool) $wishlistedItems->count()]);

        // get view
        return view('customer.wishlist.index', compact('wishlistedItems'));
    }

    /**
     * new item to wishlist
     */
    public function store(Request $request)
    {

        // log the action
        logger()->info('[app\Http\Controllers\Customer\WishlistController@store] New item to be added in Wishlist!');

        // get the customer..
        $customer = auth()->user();
        if (!$customer) {
            // log the status..
            logger()->alert('Customer not-authenticated');

            // show error page.
            abort(403);
        }

        // validate the incoming data..
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        // log the status
        logger()->info("Data validated!", ['status' => (bool) $validated]);

        // has or add new one..
        $wishlist = $customer->wishlist()->firstOrCreate([
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id,
        ]);

        // log the status..
        logger()->info('Item added in wishlist', ['status' => (bool) $wishlist]);

        // log the end..
        logger()->info('Item wishlisting end.');

        // back with success flash message.
        return back()->with('success', 'Added to wishlist!');
    }

    /**
     * remove from wishlist
     */
    public function destroy(Wishlist $item)
    {
        // log the action
        logger()->info('[app\Http\Controllers\Customer\WishlistController@destroy] Existing item to be removed from Wishlist!');

        // remove the item..
        $removed = $item->delete();

        // log the status
        logger()->info('Item removed from wishlist?', ['status' => (bool) $removed]);

        // back with success message.
        return back()->with('success', 'Removed from wishlist!');
    }
}
