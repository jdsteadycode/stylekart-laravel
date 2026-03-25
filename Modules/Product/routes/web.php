<?php

// Route Facade Class.
use Illuminate\Support\Facades\Route;

// controller class paths..
use Modules\Product\Http\Controllers\Vendor\ProductController;
use Modules\Product\Http\Controllers\Vendor\ProductColorController;

// class path to Middleware
use App\Http\Middleware\EnsureVendorIsApproved;

// test
Route::get('/module/product/test', function () {
    return "Product Module Works!";
});

/*
** vendor routes
*/
Route::middleware(['auth', 'role:vendor', EnsureVendorIsApproved::class])
    ->prefix('module/product/vendor/products')
    ->group(function () {

        // for all products
        Route::get('/', [ProductController::class, 'index'])->name('module.vendor.products.index');

        // for new product
        Route::get('/create', [ProductController::class, 'create'])->name('module.vendor.products.create');
        Route::post('/', [ProductController::class, 'store'])->name('module.vendor.products.store');

        // for updating existing product
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('module.vendor.products.edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('module.vendor.products.update');

        // for deleting existing product
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('module.vendor.products.destroy');

        // for viewing single product
        Route::get('/{product}', [ProductController::class, 'show'])->name('module.vendor.products.show');

        // toggle status of one product
        Route::put('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
            ->name('module.vendor.products.toggle-status');

        /**
         * Colors (Sub-Module) Routes
         */
        Route::prefix('/{product}/colors')->group(function() {

            // A new color
            Route::get('/create', [ProductColorController::class, 'create'])
            ->name('module.vendor.products.colors.create');
            Route::post('/', [ProductColorController::class, 'store'])
            ->name('module.vendor.products.colors.store');

            // Edit/ Update existing Color
            Route::get('/{color}/edit', [ProductColorController::class, 'edit'])
            ->name('module.vendor.products.colors.edit');
            Route::put('/{color}', [ProductColorController::class, 'update'])
            ->name('module.vendor.products.colors.update');

            // Remove the existing color
            Route::delete('/{color}', [ProductColorController::class, 'destroy'])
            ->name('module.vendor.products.colors.destroy');

            // View the color details
            Route::get("/{color}/show", [
                ProductColorController::class,
                "show",
            ])->name("module.vendor.products.colors.show");

            /***
             * All Images related to Color
             */
            // all images
            Route::post("/{color}/images", [
                ProductColorController::class,
                "storeImages",
            ])->name("module.vendor.products.colors.images.store");

            // update the image
            Route::put("/images/{media}", [
                ProductColorController::class,
                "updateImage",
            ])->name("module.vendor.products.colors.images.update");

            // delete image
            Route::delete("/images/{media}", [
                ProductColorController::class,
                "destroyImage",
            ])->name("module.vendor.products.colors.images.destroy");
        });


        /**
         * Variant (Sub-Module) Routes
         */

});
