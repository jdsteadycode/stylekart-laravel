<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    /*
    New variant to be created
    */
    public function create(Product $product)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductVariantController@create] Product Variant creation begins",
        );

        abort_if($product->vendor_id !== auth()->id(), 403);

        return view("vendor.variants.create", compact("product"));
    }
    /*
    Save the new variant
    */
    public function store(Request $request, Product $product)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductVariantController@store] Product Variant addon begins",
        );

        // Ensure product belongs to logged-in vendor
        abort_if($product->vendor_id !== auth()->id(), 403);

        // Validate input
        $validated = $request->validate(
            [
                "size" => "required|string|max:100",
                "color_id" => "required|exists:product_colors,id",
                "sku" => "nullable|string|max:100|unique:product_variants,sku",
                "price" => "required|numeric|min:0",
                "stock" => "required|integer|min:0",
            ],
            [
                "color_id.exists" => "Select valid color",
            ],
        );

        // log the status
        Log::info("Product Variant data validated?", [
            "status" => (bool) $validated,
        ]);

        // check log
        // Log::info($validated);

        // Create variant
        $created = $product->variants()->updateOrCreate(
            [
                "color_id" => $request->color_id,
                "size" => $request->size,
            ],
            [
                "price" =>
                    $request->price == 0
                        ? $product->base_price
                        : $request->price,
                "stock" => $request->stock,
                "sku" => $request->sku
            ],
        );

        // log the status
        Log::info("Product Variant Created", [
            "status" => (bool) $created,
        ]);

        // log the end
        Log::info("Product Variant Creation ended");

        return redirect()
            ->route("vendor.products.show", $product)
            ->with("success", "Variant added successfully.");
    }

    /*
    Edit existing variant
    */
    public function edit(Product $product, ProductVariant $variant)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductVariantController@update] Product Variant Edit begins",
        );

        // Check product ownership
        abort_if($product->vendor_id !== auth()->id(), 403);

        // Ensure variant belongs to product
        abort_if($variant->product_id !== $product->id, 404);

        // log the status
        Log::info("Product Variant edit ended!");

        return view("vendor.variants.edit", compact("product", "variant"));
    }

    /*
    Update the existing variant
    */
    public function update(
        Request $request,
        Product $product,
        ProductVariant $variant,
    ) {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductVariantController@update] Product Variant Update begins",
        );

        abort_if($product->vendor_id !== auth()->id(), 403);
        abort_if($variant->product_id !== $product->id, 404);

        $validated = $request->validate(
            [
                "size" => "required|string|max:100",
                "color_id" => "required|exists:product_colors,id",
                "sku" =>
                    "nullable|string|max:100|unique:product_variants,sku," .
                    $variant->id, // sku should be unqiue except for this current record
                "price" => "required|numeric|min:0",
                "stock" => "required|integer|min:0",
            ],
            [
                "color_id.exists" => "Please select a valid color!",
            ],
        );

        // log the status
        Log::info("Variant Data validated?", ["status" => (bool) $validated]);

        // fix the price if 0
        $validated["price"] =
            $validated["price"] == 0
                ? $product->base_price
                : $validated["price"];

        // update the variant details.
        $updated = $variant->update($validated);

        // log the status
        Log::info("Variant Data updated?", ["status" => (bool) $updated]);

        // log the status
        Log::info("Product Variant update ended!");

        return redirect()
            ->route("vendor.products.show", $product)
            ->with("success", "Variant updated successfully.");
    }

    /*
    Delete existing variant
    */
    public function destroy(Product $product, ProductVariant $variant)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductVariantController@destroy] Product Variant Deletion begins",
        );

        // Vendor ownership check
        abort_if($product->vendor_id !== auth()->id(), 403);

        // Ensure variant belongs to this product
        abort_if($variant->product_id !== $product->id, 404);

        $deleted = $variant->delete();

        // log the status
        Log::info("Variant Data Deleted?", ["status" => (bool) $deleted]);

        // log the status
        Log::info("Product Variant delete ended!");

        return redirect()
            ->route("vendor.products.show", $product)
            ->with("success", "Variant deleted successfully.");
    }
}
