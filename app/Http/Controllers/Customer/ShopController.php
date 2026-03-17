<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\User;

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
        $query = Product::where('is_active', 1)->with(['colors.media', 'vendor', 'variants', 'brand']);     // new: brand loading..

        // all brands for the sidebar
        $allBrands = Brand::where('is_active', true)->get();

        // all vendors..
        $allVendors = User::where('role', 'vendor')->get();

        // all categories..
        $allCategories = Category::all();

        /**
         * price filter..
         */
        // when min-price is given and not null || ''
        if ($request->filled('min_price')) {
            // all products having more than min-price requested!
            $query->where('base_price', '>=', $request->query('min_price'));

            // log the status
            logger()->info("Products to be filtered via min-price: {$request->query('min_price')}");
        }
        // when max-price is given and not null || ''
        if ($request->filled('max_price')) {
            // all products having less than max-price requested!
            $query->where('base_price', '<=', $request->query('max_price'));

            // log the status
            logger()->info("Products to be filtered via max-price: {$request->query('max_price')}");
        }

        // if search?
        // filled -> ensures query param is not null or empty
        if ($request->filled('search')) {
            // to search?
            $toSearch = $request->query('search');

            // where product's name start with toSearch text..
            $query->where('name', 'like', "%{$toSearch}%");

            // log the status
            logger()->info('Products based on search initiated with text: ' . $toSearch);
        }

        // if filter by vendor?
        // filled -> ensures query param is not null or empty
        if ($request->filled('vendor') && is_array($request->vendor)) {

            // get vendors array
            $vendors = [...$request->vendor];

            // where products are from vendor?
            $query->whereIn('vendor_id', $vendors);

            // log the status..
            logger()->info('Products from vendors ', ['vendors' => $vendors]);
        }

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

        // if filter by brand
        if ($request->filled('brand')) {
            // from all brands
            $brandSlugs = (array) $request->brand;

            // check if asked brand is one of 'em if so, get according
            $query->whereHas('brand', function ($q) use ($brandSlugs) {
                $q->whereIn('slug', $brandSlugs);
            });

            // log the status
            logger()->info('Products filtered by brands', ['slugs' => $brandSlugs]);
        }

        // or get all products.. (6 per page)
        $products = $query->latest()->paginate(6);

        // check
        if ($products->isEmpty()) {
            // log the status
            logger()->warning('No Products found!', ['total' => $products->count()]);
        }

        // log the status
        logger()->info('Products fetched for shop page', ['status' => (bool) $products, 'total' => $products->count()]);

        // get wishlisted variants
        $wishlistVariants = auth()->user()?->wishlist()->pluck('variant_id')->toArray() ?? [];

        // send the view
        return view('customer.shop.index', compact(['products', 'allVendors', 'allBrands', 'allCategories', 'wishlistVariants']));
    }
}
