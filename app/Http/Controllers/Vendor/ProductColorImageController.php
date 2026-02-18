<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductColorImageController extends Controller
{
    /*
    When images per product & color to be saved..
    */
    public function store(Request $request, Product $product)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductColorImageController@store] Images storage by Color & product initiated",
        );

        // check if not authorized
        abort_if(!$request->user()->id, 403);

        // validate the incoming data
        $request->validate(
            [
                "color" => ["required", "string", "max:50"], // color must be string and within limit
                "images" => ["required", "array"], // ensure incoming images are in array
                "images.*" => [
                    "image",
                    "mimes:jpeg,png,jpg,gif,webp",
                    "max:5120",
                ], // image types & max 5MB
            ],
            [
                "color.required" => "Enter a color name",
                "color.string" =>
                    "Color name must be valid text! For ex: indigo",
                "color.max" => "Color length must be within 50 characters",

                "images.required" => "Select an image",
                "images.array" => ["Image must be sent in array format"],
            ],
        );

        // save the color and product details or override the existing
        $saved = $product->colorImages()->updateOrCreate(
            [
                "color" => $request->color,
            ],
            [
                "color" => $request->color,
            ],
        );

        // log the status
        Log::info("Color details saved!", ["status" => (bool) $saved]);

        // check if files exist
        if ($request->hasFile("images")) {
            // iterate over them
            foreach ($request->file("images") as $file) {
                // save image details to media table
                $saved->addMedia($file)->toMediaCollection("color_images");

                // check log
                Log::info(
                    "{$file->getClientOriginalName()} is saved to color: {$saved->color}",
                    ["status" => (bool) $saved],
                );
            }
        }

        // Log the end
        Log::info("Images by Color & product storage complete");

        return redirect()
            ->back()
            ->with("success", "Images saved to {$saved->color} Successfully");
    }

    /*
    When existing image is to be replaced.
    */
    public function update(Request $request, Product $product, Media $media)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductColorImageController@update] Existing image update initiated",
        );

        // check if not authorized
        abort_if(!$request->user()->id, 403);

        // validate the incoming data
        $request->validate(
            [
                "image" => ["required", "max:5024"],
                // image types & max 5MB
            ],
            [
                "image.required" => "Select an image for update",
                "image.max" => ["Image must be upto 5 MB"],
            ],
        );

        // remove the current image from storage and db
        $removed = $media->delete();

        // log the status
        Log::info("Existing image removed!", ["status" => (bool) $removed]);

        // check if files exist
        if ($request->hasFile("image")) {
            // save the new image
            $saved = $media->model
                ->addMedia($request->file("image"))
                ->toMediaCollection("color_images");

            // log the status
            Log::info("Image updated!", ["status" => (bool) $saved]);
        }

        // Log the end
        Log::info("Image update complete");

        return redirect()->back()->with("success", "Image updated!");
    }

    /*
    When existing image is to be removed.
    */
    public function destroy(Request $request, Product $product, Media $media)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductColorImageController@update] Existing image removal initiated",
        );

        // check if not authorized
        abort_if(!$request->user()->id, 403);

        // remove the current image from storage and db
        $removed = $media->delete();

        // log the status
        Log::info("Existing image removed!", ["status" => (bool) $removed]);

        // Log the end
        Log::info("Image removal complete");

        return redirect()->back()->with("success", "Image deleted!");
    }
}
