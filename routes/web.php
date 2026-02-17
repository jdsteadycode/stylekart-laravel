<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// for admin
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\VendorController;

use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ProductVariantController;
use App\Http\Controllers\Vendor\ProductImageController;
use App\Http\Controllers\Vendor\DashboardController;

// for vendor
use App\Http\Controllers\Vendor\VendorProfileController;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/dashboard", function () {
    return view("dashboard");
})
    ->middleware(["auth", "verified"])
    ->name("dashboard");

// Admin dashboard
Route::middleware(["auth", "role:admin"])
    ->prefix("dashboard/admin")
    ->group(function () {
        // 'dashboard/admin/'
        Route::get("/", [
            App\Http\Controllers\Admin\DashboardController::class,
            "index",
        ])->name("dashboard.admin");

        /*
        categories
        module
    */
        // 'dashboard/admin/categories'
        Route::get("/categories", [CategoryController::class, "index"])->name(
            "admin.categories.index",
        );

        // 'dashboard/admin/categories/create'
        Route::get("/categories/create", [
            CategoryController::class,
            "create",
        ])->name("admin.categories.create");

        // 'dashboard/admin/categories/store'
        Route::post("/categories/store", [
            CategoryController::class,
            "store",
        ])->name("admin.categories.store");

        // 'dashboard/admin/categories/3/edit'
        Route::get("/categories/{category}/edit", [
            CategoryController::class,
            "edit",
        ])->name("admin.categories.edit");

        // 'dashboard/admin/categories/3'
        Route::put("/categories/{category}", [
            CategoryController::class,
            "update",
        ])->name("admin.categories.update");

        // 'dashboard/admin/categories/3'
        Route::delete("/categories/{category}/delete", [
            CategoryController::class,
            "destroy",
        ])->name("admin.categories.destroy");

        /*
        subcategories
        module
    */
        // 'dashboard/admin/subcategories'
        Route::get("/subcategories", [
            SubcategoryController::class,
            "index",
        ])->name("admin.subcategories.index");

        // 'dashboard/admin/subcategories/create'
        Route::get("/subcategories/create", [
            SubcategoryController::class,
            "create",
        ])->name("admin.subcategories.create");

        // 'dashboard/admin/subcategories/store'
        Route::post("/subcategories", [
            SubcategoryController::class,
            "store",
        ])->name("admin.subcategories.store");

        // 'dashboard/admin/subcategories/4/edit'
        Route::get("/subcategories/{subcategory}/edit", [
            SubcategoryController::class,
            "edit",
        ])->name("admin.subcategories.edit");

        // 'dashboard/admin/subcategories/4'
        Route::put("/subcategories/{subcategory}/", [
            SubcategoryController::class,
            "update",
        ])->name("admin.subcategories.update");

        // 'dashboard/admin/subcategories/4/delete'
        Route::delete("/subcategories/{subcategory}/delete", [
            SubcategoryController::class,
            "destroy",
        ])->name("admin.subcategories.destroy");

        /*
            Vendors Module
        */
        // '/dashboard/admin/vendors'
        Route::get("/vendors", [VendorController::class, "index"])->name(
            "admin.vendors.index",
        );

        // '/dashboard/admin/vendors/7'  (i.e., updating status)
        Route::put("/vendors/{vendor}", [
            VendorController::class,
            "update",
        ])->name("admin.vendors.update");

        // '/dashboard/admin/vendors/7'
        Route::get("/vendors/{vendor}", [
            VendorController::class,
            "show",
        ])->name("admin.vendors.show");
    });

// Vendor dashboard
Route::middleware(["auth", "role:vendor"])
    ->prefix("dashboard/vendor")
    ->group(function () {
        /*
        dashboard module
        */

        // 'dashboard/vendor/'
        Route::get("/", [DashboardController::class, "index"])->name(
            "dashboard.vendor",
        );

        /*
            profile module
        */
        // 'dashboard/vendor/profile' - GET
        Route::get("/profile", [VendorProfileController::class, "edit"])->name(
            "vendor.profile.edit",
        );

        // 'dashboard/vendor/profile' - PUT
        Route::put("/profile", [
            VendorProfileController::class,
            "update",
        ])->name("vendor.profile.update");

        /*
            products module
        */
        // 'dashboard/vendor/products'
        Route::get("/products", [ProductController::class, "index"])->name(
            "vendor.products.index",
        );

        // 'dashboard/vendor/products/create' - for product creation form
        Route::get("/products/create", [
            ProductController::class,
            "create",
        ])->name("vendor.products.create");

        // 'dashboard/vendor/products' - store the created product
        Route::post("/products", [ProductController::class, "store"])->name(
            "vendor.products.store",
        );

        // 'dashboard/vendor/products/6/edit' - edit the created product
        Route::get("/products/{product}/edit", [
            ProductController::class,
            "edit",
        ])->name("vendor.products.edit");

        // 'dashboard/vendor/products/6/' - update the created product
        Route::put("/products/{product}", [
            ProductController::class,
            "update",
        ])->name("vendor.products.update");

        // 'dashboard/vendor/products/6' - delete the created product
        Route::delete("/products/{product}", [
            ProductController::class,
            "destroy",
        ])->name("vendor.products.destroy");

        // 'dashboard/vendor/products/6' - show the product and related attribute details
        Route::get("/products/{product}", [
            ProductController::class,
            "show",
        ])->name("vendor.products.show");

        // 'dashboard/vendor/products/6/toggle-status' - toggle the product state (active / in-active)
        Route::put("/products/{product}/toggle-status", [
            ProductController::class,
            "toggleStatus",
        ])->name("vendor.products.toggle-status");

        /*
        Product variant routes
        */
        Route::prefix("products/{product}/variants")->group(function () {
            Route::get("/create", [
                ProductVariantController::class,
                "create",
            ])->name("vendor.products.variants.create");

            Route::post("/", [ProductVariantController::class, "store"])->name(
                "vendor.products.variants.store",
            );

            Route::get("/{variant}/edit", [
                ProductVariantController::class,
                "edit",
            ])->name("vendor.products.variants.edit");

            Route::put("/{variant}", [
                ProductVariantController::class,
                "update",
            ])->name("vendor.products.variants.update");

            Route::delete("/{variant}", [
                ProductVariantController::class,
                "destroy",
            ])->name("vendor.products.variants.destroy");
        });

        // 'dashboard/vendor/product/4/images'
        Route::post("products/{product}/images", [
            ProductImageController::class,
            "store",
        ])->name("vendor.products.images.store");

        // 'dashboard/vendor/product/4/images/img-file'
        Route::delete("products/{product}/images/{image}", [
            ProductImageController::class,
            "destroy",
        ])->name("vendor.products.images.destroy");
    });

// Customer dashboard
Route::get("/dashboard/customer", function () {
    return view("dashboard.customer");
})
    ->middleware(["auth", "role:customer"])
    ->name("dashboard.customer");

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy",
    );
});

require __DIR__ . "/auth.php";
