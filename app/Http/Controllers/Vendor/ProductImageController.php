<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /*
    Store Product Images
    */
    public function store(Request $request, Product $product)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductImageController@store] Product Images store initiated",
        );

        abort_if($product->vendor_id !== auth()->id(), 403);

        $request->validate([
            "images.*" => "required|image|mimes:jpg,jpeg,png,webp|max:2048",
        ]);

        // count images
        $imagesCount = 0;

        // for each image
        foreach ($request->file("images") as $image) {
            // store image
            $path = $image->store("products", "public");

            // log the action
            Log::info("$image stored!", ["status" => (bool) $path]);

            // save the image path
            $pathSaved = $product->images()->create([
                "image_url" => $path,
                "is_primary" => $imagesCount == 0 ? 1 : 0,
                "sort_order" => $imagesCount,
            ]);

            // Log the action
            Log::info("$image path saved!", ["status" => (bool) $pathSaved]);

            // update count
            $imagesCount++;
        }

        // Log the end
        Log::info("product image store complete.");

        return back()->with("success", "Images uploaded successfully.");
    }
    /*
    delete the existing image.
    */
    public function destroy(Product $product, ProductImage $image)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductImageController@destroy] Product Image delete intiated!",
        );

        // Security checks
        abort_if($product->vendor_id !== auth()->id(), 403);
        abort_if($image->product_id !== $product->id, 404);

        // Delete physical file first
        if (Storage::disk("public")->exists($image->image_url)) {
            // remove from path
            $deleted = Storage::disk("public")->delete($image->image_url);

            // log the status
            Log::info("Image removed from Storage!", [
                "status" => (bool) $deleted,
            ]);
        }

        // Delete DB record
        $deletedFromDb = $image->delete();

        // log the status
        Log::info("Image removed from db!", [
            "status" => (bool) $deletedFromDb,
        ]);

        // log the status
        Log::info("Image deletion complete");

        return back()->with("success", "Image deleted successfully.");
    }
}
