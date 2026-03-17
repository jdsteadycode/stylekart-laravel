<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Models\Product;


class HomeController extends Controller
{
    /*
    Home page
    */
    public function index()
    {

        // log the action
        logger()->info("[app\Http\Controllers\Customer\HomeController@index] Home page requested.");

        // get categories at-max three..
        $categories = Category::limit(3)->get();

        // get the some products (about 4-5)
        $products = Product::limit(4)->with(['vendor', 'colors.media', 'brand'])->get();

        // NEW: Fetch brands that have a logo (irrespective of vendor)
        $brands = Brand::where('is_active', true)
            ->whereHas('media', function ($query) {
                $query->where('collection_name', 'brand_logos');
            })
            ->limit(6)
            ->get();

        // check
        if ($products->isEmpty()) {
            logger()->warning('OOPS! No Products exist.');
        }

        // log the status
        logger()->info("Products fetched!", ["total" => $products->count()]);
        logger()->info("Brands fetched for Home Page!", ["total" => $brands->count()]);

        // log the end.
        logger()->info('Home Page request complete.');

        // send the view..
        return view('customer.home.index', ['categories' => $categories,  'products' => $products, 'brands' => $brands]);
    }
}
