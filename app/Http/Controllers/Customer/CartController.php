<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /*
    show cart
    */
    public function index()
    {
        // log the action
        logger()->info('[app\Http\Controllers\Customer\CartController@index] Cart Page requested');

        // log the end
        logger()->info('Cart Page end');
        return view('customer.cart.index');
    }


    /*
    Add to bag,
    */
    public function store(Request $request)
    {

        // log the action
        logger()->info('[app\Http\Controllers\Customer\CartController@add] Variant add to cart initiated');

        // validate the data
        $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:5']
        ]);

        // get variant with product it belongs to..
        $variant = ProductVariant::with(['product', 'color'])->find($request->variant_id);

        // check variant details?
        if (!$variant) {
            // log the status
            logger()->alert("Variant not found: {$request->variant_id}");
            abort(404);
        }

        // check if stock is available enough
        if ($variant->stock < 1) {

            // log the status
            logger()->alert($variant->id  . 'variant is Out Of Stock!');

            // redirect client back
            return redirect()->back()->with('error', 'This item is out of stock');
        }

        // set qty to ensure incoming qty doesn't exceed the stock of variant.
        $qty = min($request->qty, $variant->stock);

        // prepare the bag..
        $bag = Session::get('bag', []);

        // if variant already exists?
        if (isset($bag[$variant->id])) {

            // update it's qty
            $bag[$variant->id]['qty'] = min($bag[$variant->id]['qty'] + $qty, $variant->stock);

            // log status
            logger()->info('existing variant qty updated', ['qty' => $bag[$variant->id]['qty']]);
        } else {

            // otherwise, add variant.
            $bag[$variant->id] = [
                'variant_id' => $variant->id,
                'product_name' => $variant->product?->name,
                'qty' => $qty,
                'price' => $variant->price ?? $variant->product?->base_price,
                'color' => $variant->color->name,
                'stock' => $variant->stock,
                'size' => $variant->size
            ];

            // log the status
            logger()->info('New variant added!', ['status' =>  (bool) $bag[$variant->id]]);
        }

        // save bag
        Session::put('bag', $bag);

        // redirect back with success
        return redirect()->back()->with('success', "Added item to bag");
    }

    /*
    Update Variant qty
    */
    public function update(Request $request, $variantId)
    {

        // log the action..
        logger()->info('[app\Http\Controllers\Customer\CartController@update] Item Qty update initiated');

        // get bag
        $bag = Session::get('bag', []);

        // check if variant exists?
        if (!isset($bag[$variantId])) {

            // log the status
            logger()->alert('Variant not found! Item Qty Update terminated.');

            // return silently..
            return redirect()->back();
        }

        // get the action
        $action = $request->action;

        // according to actions [increase/ decrease]
        switch ($action) {

            // when decrease in qty!
            case "decrease":
                // when qty is exactly 1.
                if ($bag[$variantId]['qty'] == 1) {

                    // remove the variant..
                    return $this->destroy($variantId);
                }
                // when qty is above 1
                else if ($bag[$variantId]['qty'] > 1) {

                    // update qty
                    $bag[$variantId]['qty']--;

                    // log the status
                    logger()->info('decreased qty for ' . $variantId);
                }
                break;


            // when increase in qty!
            case "increase":
                // current qty
                $currentQty = $bag[$variantId]['qty'];
                $currentStock = $bag[$variantId]['stock'];

                // check stock
                if ($currentQty < $currentStock) {

                    // update qty
                    $bag[$variantId]['qty']++;

                    // log the status
                    logger()->info('increased qty for ' . $variantId);
                }
                break;

            // default action
            default:
                logger()->info('Invalid Action. Item Qty update terminated.');
                return redirect()->back();
        }

        // update the bag
        Session::put('bag', $bag);

        // log the end
        logger()->info('Cart Quantity Update Complete. Action: ' . $action);
        return redirect()->back();
    }

    /*
    remove item from cart..
    */
    public function destroy($variantId)
    {

        // log the action
        logger()->info('[app\Http\Controllers\Customer\CartController@index] Item removal from Cart initiated');

        // get bag
        $bag = Session::get('bag', []);

        // check if variant exists?
        if (isset($bag[$variantId])) {

            // remove the variant / item
            unset($bag[$variantId]);

            // log the status
            logger()->info('Item removed from bag');

            // update the bag
            Session::put('bag', $bag);

            // log the status
            logger()->info('Bag updated');
        }

        // log the end
        logger()->info('Cart Item removal complete.');

        // redirect back
        return redirect()->back();
    }
}
