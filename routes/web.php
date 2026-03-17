<?php

// use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// for admin
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\DeliveryController;

// for vendor
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ProductVariantController;
// use App\Http\Controllers\Vendor\ProductImageController;
use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\ProductColorController;
use App\Http\Controllers\Vendor\DiscountController;
use App\Http\Controllers\Vendor\VendorOrderController;
use App\Http\Controllers\Vendor\NotificationController;
use App\Http\Controllers\Vendor\BrandController;

// use App\Http\Controllers\Vendor\ProductColorImageController;

// for customers (both)
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ShopController;
use App\Http\Controllers\Customer\ProductDetailController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\WishlistController;

use App\Models\ProductColor;
use Illuminate\Http\Request;

use App\Http\Middleware\EnsureVendorIsApproved;


/*
 */

Route::get("/test-upload", function () {
    return view("test-upload");
});

// Route::get('/order-success', function () {
//     return view('customer.pages.order-success');
// });

Route::post("/test-upload", function (Request $request) {
    $request->validate([
        "product_id" => "required|exists:products,id",
        "name" => "required|string",
        "image" => "required|image",
    ]);

    // Create color record
    $color = ProductColor::create([
        "product_id" => $request->product_id,
        "name" => $request->color,
    ]);

    // Attach media
    $color
        ->addMedia($request->file("image"))
        ->toMediaCollection("color_images");

    return back()->with("success", "Image uploaded successfully!");
})->name("test-upload-image");

/*

*/

// for vendor
use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Middleware\CheckRole;

Route::get('/', function () {
    return "Working fine!";
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

        /**
         * deliveries module
         */
        Route::prefix('deliveries')->group(function () {

            // 'dashboard/admin/deliveries/' -> all pending orders to be delivered!
            Route::get('/', [DeliveryController::class, 'index'])->name('admin.deliveries.index');

            // 'dashboard/admin/deliveries/assign' -> assign to delivery person
            Route::post('/assign', [DeliveryController::class, 'assign'])->name('admin.deliveries.assign');
        });

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
        Route::get("/", [DashboardController::class, "index"])
            ->name("dashboard.vendor")
            ->middleware(EnsureVendorIsApproved::class);

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


        /***
         * Brands Module
         */
        // List all brands
        Route::get('/vendor/brands', [BrandController::class, 'index'])
            ->name('vendor.brands.index');

        // Show create form
        Route::get('/vendor/brands/create', [BrandController::class, 'create'])
            ->name('vendor.brands.create');

        // Store new brand
        Route::post('/vendor/brands/store', [BrandController::class, 'store'])
            ->name('vendor.brands.store');

        // Show edit form
        Route::get('/vendor/brands/{brand}/edit', [BrandController::class, 'edit'])
            ->name('vendor.brands.edit');

        // Update brand
        Route::put('/vendor/brands/{brand}/update', [BrandController::class, 'update'])
            ->name('vendor.brands.update');

        // Delete brand
        Route::delete('/vendor/brands/{brand}', [BrandController::class, 'destroy'])
            ->name('vendor.brands.destroy');


        /**
         * notifications
         */
        Route::prefix('vendor/notifications')->name('vendor.notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('markAllRead');
        });

        /*
            Protected Routes for access if ain't approved..
        */
        Route::middleware(EnsureVendorIsApproved::class)->group(function () {
            /*
               products module
           */
            Route::prefix("products")->group(function () {
                // 'dashboard/vendor/products'
                Route::get("/", [ProductController::class, "index"])->name(
                    "vendor.products.index",
                );

                // 'dashboard/vendor/products/create' - for product creation form
                Route::get("/create", [
                    ProductController::class,
                    "create",
                ])->name("vendor.products.create");

                // 'dashboard/vendor/products' - store the created product
                Route::post("/", [ProductController::class, "store"])->name(
                    "vendor.products.store",
                );

                // 'dashboard/vendor/products/6/edit' - edit the created product
                Route::get("/{product}/edit", [
                    ProductController::class,
                    "edit",
                ])->name("vendor.products.edit");

                // 'dashboard/vendor/products/6/' - update the created product
                Route::put("/{product}", [
                    ProductController::class,
                    "update",
                ])->name("vendor.products.update");

                // 'dashboard/vendor/products/6' - delete the created product
                Route::delete("/{product}", [
                    ProductController::class,
                    "destroy",
                ])->name("vendor.products.destroy");

                // 'dashboard/vendor/products/6' - show the product and related attribute details
                Route::get("/{product}", [
                    ProductController::class,
                    "show",
                ])->name("vendor.products.show");

                // 'dashboard/vendor/products/6/toggle-status' - toggle the product state (active / in-active)
                Route::put("/{product}/toggle-status", [
                    ProductController::class,
                    "toggleStatus",
                ])->name("vendor.products.toggle-status");
            });

            /*
           Product Color routes
           */
            Route::prefix("products/{product}/colors")->group(function () {
                // 'dashboard/vendor/{4}/colors/create'
                Route::get("/create", [
                    ProductColorController::class,
                    "create",
                ])->name("vendor.products.colors.create");

                // 'dashboard/vendor/{4}/colors/'
                Route::post("/", [
                    ProductColorController::class,
                    "store",
                ])->name("vendor.products.colors.store");

                // 'dashboard/vendor/{4}/colors/12'
                Route::get("/{color}", [
                    ProductColorController::class,
                    "edit",
                ])->name("vendor.products.colors.edit");

                // 'dashboard/vendor/{4}/colors/12'
                Route::put("/{color}", [
                    ProductColorController::class,
                    "update",
                ])->name("vendor.products.colors.update");

                // 'dashboard/vendor/{4}/colors/12'
                Route::delete("/{color}", [
                    ProductColorController::class,
                    "destroy",
                ])->name("vendor.products.colors.destroy");

                // 'dashboard/vendor/{4}/colors/12'
                Route::get("/{color}/show", [
                    ProductColorController::class,
                    "show",
                ])->name("vendor.products.colors.show");

                // 'dashboard/vendor/{4}/colors/12/images'
                Route::post("/{color}/images", [
                    ProductColorController::class,
                    "storeImages",
                ])->name("vendor.colors.images.store");

                // Replace image (PUT)
                Route::put("/images/{media}", [
                    ProductColorController::class,
                    "updateImage",
                ])->name("vendor.colors.images.update");

                // Delete image (DELETE)
                Route::delete("/images/{media}", [
                    ProductColorController::class,
                    "destroyImage",
                ])->name("vendor.colors.images.destroy");
            });

            /*
           Product variant routes
           */
            Route::prefix("products/{product}/variants")->group(function () {
                Route::get("/create", [
                    ProductVariantController::class,
                    "create",
                ])->name("vendor.products.variants.create");

                Route::post("/", [
                    ProductVariantController::class,
                    "store",
                ])->name("vendor.products.variants.store");

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


            /**
             * Product discount routes
             */
            Route::prefix("discounts")->group(function () {
                // 'dashboard/vendor/discounts' - list all discounts
                Route::get("/", [DiscountController::class, "index"])->name(
                    "vendor.discounts.index"
                );

                // 'dashboard/vendor/discounts/create' - show the create form
                Route::get("/create", [DiscountController::class, "create"])->name(
                    "vendor.discounts.create"
                );

                // 'dashboard/vendor/discounts' - store the new discount
                Route::post("/", [DiscountController::class, "store"])->name(
                    "vendor.discounts.store"
                );

                // 'dashboard/vendor/discounts/32/toggle' - updates current discount status
                Route::patch('/{discount}/toggle', [DiscountController::class, 'toggle'])->name('vendor.discounts.toggle');

                // 'dashboard/vendor/discounts/32/edit'
                Route::get("/{discount}/edit", [DiscountController::class, "edit"])->name("vendor.discounts.edit");

                // 'dashboard/vendor/discounts/32' (PUT/PATCH for update)
                Route::put("/{discount}", [DiscountController::class, "update"])->name("vendor.discounts.update");

                // 'dashboard/vendor/discounts/32' - deletes the discount
                Route::delete("/{discount}", [DiscountController::class, "destroy"])->name("vendor.discounts.destroy");
            });

            /*
            Orders Module
            */
            Route::prefix("orders")->group(function () {

                // List vendor's incoming orders
                Route::get("/", [VendorOrderController::class, "index"])
                    ->name("vendor.orders.index");

                // Show specific order (only vendor’s items)
                Route::get("/{order}", [VendorOrderController::class, "show"])
                    ->name("vendor.orders.show");

                // Update item status
                Route::put("/items/{item}/status", [VendorOrderController::class, "updateStatus"])
                    ->name("vendor.orders.items.update-status");

                // cancel an ordered item..
                Route::patch(
                    '/items/{item}/cancel',
                    [VendorOrderController::class, 'cancel']
                )->name('vendor.orders.cancel');
            });
        });
    });

// StyleKart public view / site..
Route::prefix("/stylekart-store")->group(function () {

    // '/' - home page..
    Route::get("/", [HomeController::class, 'index'])->name("customer.home");

    // '/shop' - shop page..
    Route::get("/shop", [ShopController::class, 'index'])->name("customer.shop");

    // '/product/2' - single product page..
    Route::get("/product/{product}", [ProductDetailController::class, 'show'])->name("customer.product.show");

    // bag (cart) routes
    Route::prefix('cart')->name('customer.cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');       // show bag
        Route::post('/', [CartController::class, 'store'])->name('store');      // add item
        Route::patch('/{variant}', [CartController::class, 'update'])->name('update'); // update qty
        Route::delete('/{variant}', [CartController::class, 'destroy'])->name('destroy'); // remove item
    });

    // protected routes..
    // authenticated as well as it should be customer.
    Route::middleware(['auth', 'role:customer'])->group(function () {

        // Wishlist
        Route::prefix('wishlist')->name('customer.wishlist.')->group(function () {
            Route::get('/', [WishlistController::class, 'index'])->name('index');       // show wishlist
            Route::post('/', [WishlistController::class, 'store'])->name('store');       // add item
            Route::delete('/{item}', [WishlistController::class, 'destroy'])->name('destroy'); // remove item
        });


        // 'stylekart-store/profile' -> view profile and rest of details..
        Route::get('/profile', [ProfileController::class, 'index'])->name('customer.profile');

        // 'stylekart-store/profile' -> update the existing profile
        Route::patch('/profile', [ProfileController::class, 'update'])->name('customer.profile.update');

        // for address..
        // inside Route::middleware(['auth', 'role:customer'])->group(...)
        Route::prefix('address')->name('customer.address.')->group(function () {

            // Show create form
            Route::get('/create', [AddressController::class, 'create'])
                ->name('create');

            // Store new address
            Route::post('/', [AddressController::class, 'store'])
                ->name('store');

            // Show edit form
            Route::get('/{address}/edit', [AddressController::class, 'edit'])
                ->name('edit');

            // Update existing address
            Route::patch('/{address}', [AddressController::class, 'update'])
                ->name('update');

            // Delete address
            Route::delete('/{address}', [AddressController::class, 'destroy'])
                ->name('destroy');
        });

        // 'orders/{order}' - view single order by customer.
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('customer.orders.show');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'showInvoice'])
            ->name('customer.orders.invoice');



        // 'stylekart-store/checkout' - initial data for checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('customer.checkout');

        // 'stylekart-store/checkout' - handle checkout
        Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('customer.checkout.placeOrder');

        // 'stylekart-store/payment/mock/XXX/' - mock online payment
        Route::get('/payment/mock/{orderNumber}',  [PaymentController::class, 'index'])->name('customer.payment.mock');

        // 'stylekart-store/payment/mock/XXX/' - mock online payment
        Route::post('/payment/mock/{orderNumber}/process',  [PaymentController::class, 'process'])->name('customer.payment.mock.process');

        // 'stylekart-store/order/XXXXX' -> for order success
        Route::get('/order/{orderNumber}', [CheckoutController::class, 'success'])->name('customer.checkout.success');

        // 'stylekart-store/order/XXXX/item/2/cancel' -> for order item cancellation
        Route::post('/order/{orderNumber}/{item}/cancel', [OrderController::class, 'cancelItem'])
            ->name('customer.order-item.cancel');

        // 'stylekart-store/order/XXXX/cancel' -> for order item cancellation
        Route::post('/order/{orderNumber}/cancel', [OrderController::class, 'cancelFullOrder'])
            ->name('customer.orders.cancel');

        // 'stylekart-store/order/fail' -> when order placement fails
        // Route::get('/order/fail', fn() =>  view('customer.checkout.failed'))->name('customer.checkout.fail');
    });
});

// Delivery Person Dashboard
Route::middleware(['auth', 'role:delivery_person'])
    ->prefix('dashboard/delivery')
    ->group(function () {
        // 'dashboard/delivery/'
        Route::get('/', [App\Http\Controllers\Delivery\DashboardController::class, 'index'])
            ->name('dashboard.delivery');

        // 'dashboard/delivery/orders/{order}/'
        Route::get('/orders/{order}', [App\Http\Controllers\Delivery\DashboardController::class, 'showJob'])
            ->name('delivery.order.show');

        // 'dashboard/delivery/orders/{order}/accept'
        Route::post('/orders/{order}/accept', [App\Http\Controllers\Delivery\DashboardController::class, 'accept'])
            ->name('delivery.order.accept');

        // 'dashboard/delivery/orders/{order}/complete'
        Route::post('/orders/{order}/complete', [App\Http\Controllers\Delivery\DashboardController::class, 'complete'])
            ->name('delivery.order.complete');

        // notifications
        Route::prefix('/notifications')->name('delivery.notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Delivery\NotificationController::class, 'index'])->name('index');
            Route::post('/markRead', [App\Http\Controllers\Delivery\NotificationController::class, 'markRead'])->name('markRead');
        });
    });

/*
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
*/

require __DIR__ . "/auth.php";
