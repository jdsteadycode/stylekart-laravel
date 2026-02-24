<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
        $products = Product::limit(4)->with(['vendor', 'colors.media'])->get();

        // check
        if ($products->isEmpty()) {
            logger()->warning('OOPS! No Products exist.');
        }

        // log the status
        logger()->info("Products fetched!", ["total" => $products->count()]);

        // log the end.
        logger()->info('Home Page request complete.');

        // send the view..
        return view('customer.home.index', ['categories' => $categories,  'products' => $products]);
    }
}
