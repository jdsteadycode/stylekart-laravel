<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;

class ShopController extends Controller
{
    /*
    for all products in shop page
    */
    public function index(Request $request)
    {
        // Log the action
        logger()->info('[app\Http\Controllers\Customer\ShopController@index] Shop page products requested.');

        // all active products..
        $query = Product::where('is_active', 1)->with('colors.media');

        // of specific main category (Men, Women etc)
        // filled -> ensures query param is not null or empty
        if ($request->filled('category')) {

            // when each subcategory
            $query->whereHas('subCategory', function ($subQuery) use ($request) {

                // is related to given category's id.
                $subQuery->where('category_id', $request->query('category'));
            });

            // log the status
            logger()->info('Products requested for category: ' . $request->query('category'));
        }

        // or get all products..
        $products = $query->latest()->get();

        // check
        if ($products->isEmpty()) {
            // log the status
            logger()->warning('No Products found!', ['total' => $products->count()]);
        }

        // log the status
        logger()->info('Products fetched for shop page', ['status' => (bool) $products, 'total' => $products->count()]);

        // send the view
        return view('customer.shop.index', compact('products'));
    }
}
