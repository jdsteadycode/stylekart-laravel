<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /*
        all products
    */
    public function index(Request $request)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductController@index] All products fetch initiated!",
        );

        // logged in user
        $vendorId = $request->user()->id;

        // log the status
        Log::info("Vendor accessing products page", [
            "vendor_id" => $vendorId,
        ]);

        // when status filter
        $status = $request->query("status");
        if ($status) {
            // log the status
            Log::info("Products with status: $status fetched");

            // get products based on status (active / in-active)
            $products = Product::where("vendor_id", $vendorId)
                ->where("deleted_at", null)
                ->where("is_active", $status === "active" ? 1 : 0)
                ->with(["subCategory"])
                ->latest()
                ->paginate(10);

            // log the status
            Log::info("Products with status: $status fetched", [
                "products" => $products,
            ]);
        }

        // all products..
        else {
            // fetch all
            $products = Product::where("vendor_id", $vendorId)
                ->where("deleted_at", null)
                ->with(["subCategory"])
                ->latest()
                ->paginate(10);
        }

        // Log the status
        Log::info("Products fetched for vendor", [
            "count" => $products->count(),
        ]);

        // log the end
        Log::info("Products fetch complete");

        // check log
        // return "all products fetched";
        return view("vendor.products.index", compact("products"));
    }

    /* for new product form */
    public function create()
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductController@create] New product creation begins",
        );

        // get the main categories with sub categories.. (early)
        $categories = Category::with("subCategories")->get();
        return view("vendor.products.create", compact("categories"));
    }

    /* for saving the new product */
    public function store(Request $request)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductController@store] New product saving begins",
        );

        // get the logged-in vendor's id
        $vendorId = $request->user()->id;

        // ensure data is clean
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string",
            "category_id" => "required",
            "sub_category_id" => "required|exists:sub_categories,id",
            "base_price" => "required|numeric|min:0",
        ]);

        // check data
        Log::info("Product data", ["product" => $validated]);

        // try to create the product
        $product = Product::create([
            "vendor_id" => $vendorId,
            "name" => $validated["name"],
            "description" => $validated["description"],
            "sub_category_id" => $validated["sub_category_id"],
            "base_price" => $validated["base_price"],
            "is_active" => 0,
        ]);

        // Log the status
        Log::info("$product->name was just created by $vendorId", [
            "status" => (bool) $product,
        ]);

        // log the end
        Log::info("Product creation end.");

        return redirect()
            ->route("vendor.products.index")
            ->with(
                "success",
                "Product created. Click on it to add variants and images.",
            );
    }

    /*
        show edit product form
    */
    public function edit(Product $product)
    {
        abort_if($product->vendor_id !== auth()->id(), 403);

        $categories = Category::with("subCategories")->get();

        return view("vendor.products.edit", compact("product", "categories"));
    }

    /*
        update existing product
    */
    public function update(Request $request, Product $product)
    {
        abort_if($product->vendor_id !== auth()->id(), 403);

        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductController@update] Product updation begins",
        );

        // ensure data is clean
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string",
            "category_id" => "required",
            "sub_category_id" => "required|exists:sub_categories,id",
            "base_price" => "required|numeric|min:0",
        ]);

        // update the product
        $updated = $product->update($validated);

        // Log status
        Log::info("Product updated", ["status" => (bool) $updated]);

        // log the end
        Log::info("Product update ended.");

        return redirect()
            ->route("vendor.products.index")
            ->with("success", "Product updated successfully.");
    }

    /*
        remove existing product
    */
    public function destroy(Product $product)
    {
        abort_if($product->vendor_id !== auth()->id(), 403);

        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductController@destroy] Product deletion begins",
        );

        // delete the product
        $deleted = $product->delete();

        // Log status
        Log::info("Product deleted", ["status" => (bool) $deleted]);

        // log the end
        Log::info("Product deletion ended.");

        return redirect()
            ->route("vendor.products.index")
            ->with("success", "Product deleted successfully.");
    }

    /*
        show existing product and attributes
    */
    public function show(Product $product)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductController@show] Single Product view begins",
        );

        abort_if($product->vendor_id !== auth()->id(), 403);

        $product->load(["subCategory.category", "variants"]);

        // log the end
        Log::info("Single Product view ended.");

        return view("vendor.products.show", compact("product"));
    }

    /*
    toggle the product status
    */
    public function toggleStatus(Product $product, Request $request)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductController@toggleStatus] Product status toggle iniatiated",
        );

        // get the authenticated user
        $vendor = $request->user();

        // Ensure vendor owns this product
        abort_if($product->vendor_id !== $vendor->id, 403);

        // Toggle status
        $product->is_active = !$product->is_active;
        $toggled = $product->save();

        // log the status
        Log::info("Product status changed", ["status" => (bool) $toggled]);

        // Log the end
        Log::info("Single Product view ended.");

        // Redirect back with a message
        return redirect()->back()->with("success", "Product status updated.");
    }
}
