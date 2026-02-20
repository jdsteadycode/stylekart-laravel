<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductColorController extends Controller
{
    /*
    Creating a new color form
    */
    public function create(Product $product)
    {
        return view("vendor.colors.create", compact("product"));
    }

    /*
    A new color
    */
    public function store(Request $request, Product $product)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductColorController@store] Color creation initiated",
        );

        // sanitize the color data
        $validated = $request->validate(
            [
                "name" => "required|string|max:50",
            ],
            [
                "name.required" => "Color name is required.",
                "name.string" => "Color name must be text",
                "name.max" => "Color name must be within 50 chars",
            ],
        );

        // log the status
        Log::info("Color data validated!", ["status" => (bool) $validated]);

        // create color for the product
        $created = $product->colors()->create([
            "name" => strtolower(trim($validated["name"])),
        ]);

        // log the status
        Log::info("Color: {$created->name} Created", [
            "status" => (bool) $created,
        ]);

        return redirect()
            ->route("vendor.products.show", $product)
            ->with("success", "Color added successfully.");
    }

    /*
    When Color update form
    */
    public function edit(Product $product, ProductColor $color)
    {
        if ($color->product_id !== $product->id) {
            // Log
            Log::alert("Invalid edit product color action");
            abort(404);
        }

        return view("vendor.colors.edit", compact("product", "color"));
    }

    /*
    Product Color update
    */
    public function update(
        Request $request,
        Product $product,
        ProductColor $color,
    ) {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductColorController@update] Color updation initiated",
        );

        if ($color->product_id !== $product->id) {
            // Log
            Log::alert("Invalid edit product color action");
            abort(404);
        }

        $validated = $request->validate(
            [
                "name" => ["required", "string", "max:50"],
            ],
            [
                "name.required" => "Color name is required.",
                "name.string" => "Color name must be text",
                "name.max" => "Color name must be within 50 chars",
            ],
        );

        // old and new color names
        $oldName = $color->name;
        $newName = strtolower(trim($validated["name"]));

        // update the color name
        $updated = $color->update([
            "name" => $newName,
        ]);

        // log status
        Log::info("Color updated!", ["status" => (bool) $updated]);

        // check if old name is updated to new one!
        if ($oldName !== $newName) {
            // check if variants contain same old color name
            // update the color name of that variant(s)
            $product
                ->variants()
                ->where("color", $oldName)
                ->update(["color" => $newName]);

            // log the action
            Log::warning("Variant color name updated!", [
                "old" => $oldName,
                "new" => $newName,
            ]);
        }

        return redirect()
            ->route("vendor.products.show", $product)
            ->with("success", "Color updated successfully.");
    }

    /*
    When color to be deleted
    */
    public function destroy(Product $product, ProductColor $color)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductColorController@destroy] Color deletion initiated",
        );

        if ($color->product_id !== $product->id) {
            // Log
            Log::alert("Invalid delete product color action");
            abort(404);
        }

        // get number of variants having same color
        $variantsOfColor = $product
            ->variants()
            ->where("color", $color->name)
            ->count();

        // check if color name is related to some variants..
        if ($variantsOfColor > 0) {
            // Log the status
            Log::alert(
                "Cannot delete the {$color->name}. Because, same color has {$variantsOfColor} variants",
            );

            // back to view
            return redirect()
                ->route("vendor.products.show", $product)
                ->with(
                    "error",
                    "Cannot delete this color because variants exist for it.",
                );
        }

        // otherwise, delete the color
        $deleted = $color->delete();

        // log the action
        Log::alert("Color removed", ["status" => (bool) $deleted]);

        return redirect()
            ->route("vendor.products.show", $product)
            ->with("success", "Color deleted successfully.");
    }

    /*
    When color details is requested
    */
    public function show(
        Request $request,
        Product $product,
        ProductColor $color,
    ) {
        return view("vendor.colors.show", compact(["color", "product"]));
    }

    /*
    When images to color is to be stored
    */
    public function storeImages(
        Request $request,
        Product $product,
        ProductColor $color,
    ) {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductColorController@storeImages] Images storage by Color initiated",
        );

        // check
        if ($product->id !== $color->product_id) {
            // Log the status
            Log::info("Invalid Request");
            abort(403);
        }

        // Validate the uploaded files
        $request->validate([
            "images.*" =>
                "required|image|mimes:jpg,jpeg,png,gif,webp,avif|max:10000",
        ]);

        // check if images exist
        if ($request->hasFile("images")) {
            // iterate each
            foreach ($request->file("images") as $file) {
                // add image according to the color
                $saved = $color
                    ->addMedia($file)
                    ->toMediaCollection("color_images");

                // log the status
                Log::info("Image saved!", ["status" => (bool) $saved]);
            }
        }

        // redirect back
        return redirect()
            ->back()
            ->with("success", "Images uploaded for color.");
    }

    /*
    When image is updated / replaced
    */
    public function updateImage(
        Request $request,
        Product $product,
        ProductColor $color,
        Media $media,
    ) {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductColorController@updateImage] Existing image update initiated",
        );

        // ensure file belongs to color only
        if ($media->model->model_id !== $color->id) {
            Log::info("File doesn't belong to the {$color->name}");
            abort(403);
        }

        // Validate the uploaded file
        $request->validate([
            "image.*" =>
                "required|image|mimes:jpg,jpeg,png,gif,webp,avif|max:5120",
        ]);

        // delete the existing image
        $deleted = $media->delete();

        // Log
        Log::info("Removing existing image for color: {$media->model->name}", [
            "status" => (bool) $deleted,
        ]);

        // check if images exist
        if ($request->hasFile("image")) {
            // add image according to the color
            $saved = $media->model
                ->addMedia($request->file("image"))
                ->toMediaCollection("color_images");

            // log the status
            Log::info("Image updated!", ["status" => (bool) $saved]);
        }

        // redirect back
        return redirect()
            ->back()
            ->with("success", "Images updated for {$media->model->name}");
    }

    /*
    When image is removed
    */
    public function destroyImage(
        Request $request,
        Product $product,
        ProductColor $color,
        Media $media,
    ) {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\ProductColorController@destroyImage] Existing image delete initiated",
        );

        // ensure file belongs to color only
        if ($media->model->model_id !== $color->id) {
            Log::info("File doesn't belong to the {$color->name}");
            abort(403);
        }

        // delete the existing image
        $deleted = $media->delete();

        // Log
        Log::info("Removing existing image for color: {$media->model->name}", [
            "status" => (bool) $deleted,
        ]);

        // redirect back
        return redirect()
            ->back()
            ->with("success", "Images deleted for {$media->model->name}");
    }
}
