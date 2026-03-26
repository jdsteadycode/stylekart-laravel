<?php

namespace Modules\Product\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// fixed: Request class path
use Modules\Product\Http\Requests\ProductRequest;

// get the Model Classes
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

// get Log Facade class
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
     {
         // Log the action
         Log::info(
             "[Modules\Product\app\Http\Controllers\Vendor\ProductController@index] All products fetch initiated!",
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
                 ->with(["subCategory", "brand"])
                 ->latest()
                 ->paginate(10);

             // log the status
             Log::info("Products with status: $status fetched", [
                 "total" => $products->count(),
             ]);
         }

         // all products..
         else {
             // fetch all
             $products = Product::where("vendor_id", $vendorId)
                 ->where("deleted_at", null)
                 ->with(["subCategory", "brand"])
                 ->latest()
                 ->paginate(10);
         }

         // Log the status
         Log::info("Products fetched for vendor", [
             "total" => $products->count(),
         ]);

         // log the end
         Log::info("Products fetch complete");

         // check log
         // return "all products fetched";
         return view("product::vendor.products.index", compact("products"));
     }
    /**
     * Show the form for creating a new resource.
     */
     public function create()
     {
         // Log the action
         Log::info(
             "[Modules\Product\app\Http\Controllers\Vendor\ProductController@create] New product creation begins",
         );

         // get brands available by this vendor
         $brands = Brand::where('vendor_id', auth()->id())
             ->where('is_active', true)
             ->get();

         // get the main categories with sub categories.. (early)
         $categories = Category::with("subCategories")->get();
         return view("product::vendor.products.create", compact("categories", "brands"));
     }

    /**
     * Store a newly created resource in storage.
     */
     public function store(ProductRequest $request)
     {
         // Log the action
         Log::info(
             "[Modules\Product\app\Http\Controllers\Vendor\ProductController@store] New product saving begins",
         );

         // get the logged-in vendor's id
         $vendorId = $request->user()->id;

         $validated = $request->validated();

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
             "brand_id" => $request->brand_id,
         ]);

         // Log the status
         Log::info("$product->name was just created by $vendorId", [
             "status" => (bool) $product,
         ]);

         // log the end
         Log::info("Product creation end.");

         return redirect()
             ->route("module.vendor.products.index")
             ->with(
                 "success",
                 "Product created. Click on it to add variants and images.",
             );
     }

    /**
     * Show the specified resource.
     */
     public function show(Product $product)
     {
         // log the action
         Log::info(
             "[Modules\Product\app\Http\Controllers\Vendor\ProductController@show] Single Product view begins",
         );

         abort_if($product->vendor_id !== auth()->id(), 403);

         // get related tables also quick (eager loading)
         $product->load(["subCategory.category", "variants", "brand"]);

         // log the end
         Log::info("Single Product view ended.");

         return view("product::vendor.products.show", compact("product"));
     }

     /*
     toggle the product status
     */
     public function toggleStatus(Product $product, Request $request)
     {
         // log the action
         Log::info(
             "[Modules\Product\app\Http\Controllers\Vendor\ProductController@toggleStatus] Product status toggle iniatiated",
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

    /**
     * Show the form for editing the specified resource.
     */
     public function edit(Product $product)
     {

         // log the action
         Log::info(
             "[Modules\Product\app\Http\Controllers\Vendor\ProductController@edit] Product Edit begins",
         );

         abort_if($product->vendor_id !== auth()->id(), 403);

         // get brands available for dropdown
         $brands = Brand::where('vendor_id', auth()->id())
             ->where('is_active', true)
             ->get();

         $categories = Category::with("subCategories")->get();

         return view("product::vendor.products.edit", compact("product", "categories", "brands"));
     }

    /**
     * Update the specified resource in storage.
     */
     public function update(ProductRequest $request, Product $product)
     {

         // log the action
         Log::info(
             "[Modules\Product\app\Http\Controllers\Vendor\ProductController@update] Product updation begins",
         );

         $validated = $request->validated();

         // add brand_id if available or null anyways
         $validated['brand_id'] = $request->brand_id;

         // update the product
         $updated = $product->update($validated);

         // Log status
         Log::info("Product updated", ["status" => (bool) $updated]);

         // log the end
         Log::info("Product update ended.");

         return redirect()
             ->route("module.vendor.products.index")
             ->with("success", "Product updated successfully.");
     }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Product $product)
     {
         abort_if($product->vendor_id !== auth()->id(), 403);

         // log the action
         Log::info(
             "[Modules\Product\app\Http\Controllers\Vendor\ProductController@destroy] Product deletion begins",
         );

         // delete the product
         $deleted = $product->delete();

         // Log status
         Log::info("Product deleted", ["status" => (bool) $deleted]);

         // log the end
         Log::info("Product deletion ended.");

         return redirect()
             ->route("module.vendor.products.index")
             ->with("success", "Product deleted successfully.");
     }
}
