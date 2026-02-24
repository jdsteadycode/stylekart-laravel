<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductDetailController extends Controller
{
    /*
    Single Product is to be shown
    */
    public function show(Product $product, Request $request)
    {

        // log the action
        logger()->info('[app\Http\Controllers\Customer\ProductDetailController@show] Product Detail initiated');

        // check if product is active?
        if (!$product->is_active) {
            // log the status
            logger()->alert('OOPS! In-active product!');
            abort(403);
        }

        // when variant is requested
        $variantId = $request->query('variant');
        $selectedVariant = $variantId ?
            $product->variants()->findOrFail($variantId)
            : $product->variants->first();

        // get the product data i.e., colors & variants
        $product =
            $product->load(['variants', 'colors.media']);

        // log the status
        logger()->info('details fetched for ' . $product->name);

        // log the end
        logger()->info('Product details complete.');

        // send the view.
        return view('customer.product.show', [
            'product' => $product,
            'selectedVariant' => $selectedVariant
        ]);
    }
}
