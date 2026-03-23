<?php

// get Model class paths
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Services\OrderService;

// get Mail Facade class
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderSuccessMail;

// get Notification Facade class
use Illuminate\Support\Facades\Notification;
use App\Notifications\Vendor\NewOrderNotification;

// RefreshDatabase trait
use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class);

/**
 * Helpers
 */

 // () -> create customer with address
 function createCustomerWithAddress(): array {
     // a customer
     $customer = User::factory()->create(['role'=>'customer']);

     // add address to it
     $address = $customer->addresses()->create([
         "name" => $customer->name,
         "phone" => 12345678890,
         "address_line" => 'Kunj Co-Op Hsg, Shankar lane, kandivli East, iraniwadi road no: 4, Mumbai Maharashtra',
         "city" => 'kandivli',
         "state" => 'Maharashtra',
         "pincode" => '400001',
         "landmark" => 'Shankar lane',
         "address_type" => 'home',
         "is_default" => true,
     ]);

     // get both of them
     return [$customer, $address];
 }

 // () -> create product with color & variant
 function createVendorProductVariant(): array {
     // a vendor
     $vendor = User::factory()->create(['role'=>'vendor']);

     // a category
     $category = Category::create([
         'name' => 'Men'
     ]);

     // a sub-category
     $subCategory = SubCategory::create([
         'name' => 'Topwear',
         'category_id' => $category->id
     ]);

     // a product
     $product = Product::create([
         'vendor_id' => $vendor->id,
         'sub_category_id' => $subCategory->id,
         'name' => 'Cotton T-shirt',
         'description' => '100% Cotton, fine and comfortable T-shirt',
         'base_price' => 0,
         'is_active' => 1,
         'brand_id' => null,
     ]);

     // a color
     $color = $product->colors()->create([
         'name' => 'grey'
     ]);

     // a variant
     $variant = $product->variants()->create([
         'color_id' => $color->id,
         'size' => 'S',
         'price' => 299,
         'stock' => 20,
         'sku' => "{$product->name}-{$color->name}-S",
     ]);

     // get all
     return [$vendor, $product, $variant];
 }
 /** */

// () -> test customer and address creation
it('creates a customer with default address', function() {

    // get customer and address
    [$customer, $address] = createCustomerWithAddress();

    // check if customer and address were created
    expect($customer, 'customer needs to be created')->not->toBeNull();

    // check if customer owns the address or not
    expect($address->user_id, 'customer should own the address')->toBe($customer->id);
});

// () -> test product with colors, variants creation
it('creates a product with color and variant', function() {

    // get vendor, product and variant.
    [$vendor, $product, $variant] = createVendorProductVariant();

    // check if variant belongs to product
    expect($variant->product_id, 'variant should belong to the product')->toBe($product->id);
});

// () -> test order creation and stock check
it('place an order, reduce stock, and send fake email and notifications', function() {

    // use fake mail
    Mail::fake();

    // use fake notification
    Notification::fake();

    // get customer and product data
    [$customer, $address] = createCustomerWithAddress();
    [$vendor, $product, $variant] = createVendorProductVariant();

    // a bag
    $bag = [
        [
            'variant_id' => $variant->id,
            'qty' => 2,
        ]
    ];

    // instantiate the order service
    $orderService = new OrderService();
    $order = $orderService->createOrder($customer, $address, ['pay' => 'cod'], $bag);

    // check if order is created?
    expect($order, 'order should be created')->not->toBeNull();

    // check quantity?
    expect($order->items->first()->quantity, 'quantity of item should be intended')->toBe(2);

    // check if stock was reduced
    expect($variant->fresh()->stock, 'variant stock should be in valid state')->toBe(18);

    // check if fake email was sent to designated customer
    Mail::assertSent(OrderSuccessMail::class, function($mail) use ($customer) {
        return $mail->hasTo($customer->email);
    });

    // check if fake notification was sent to vendor
    Notification::assertSentTo($vendor, NewOrderNotification::class);
});
