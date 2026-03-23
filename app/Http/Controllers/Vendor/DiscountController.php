<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\StoreDiscountRequest;
// get path to Model Classes
use App\Models\Discount;
use App\Models\Product;
use App\Models\SubCategory;
// get path to Request Class
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the currently logged-in vendor
        $vendorId = auth()->id();

        // log the action
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@index] Vendor ID: {$vendorId} requested their discounts list.");

        // get discounts
        $discounts = Discount::with(['product', 'subCategory'])
            ->where('vendor_id', $vendorId)
            ->latest() // from recent ones
            ->paginate(10);

        return view('vendor.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendorId = auth()->id();

        // log the action
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@create] Vendor ID: {$vendorId} requested the create discount form.");

        // 1. Fetch ONLY this vendor's active products
        $products = Product::where('vendor_id', $vendorId)
            ->where('is_active', true)
            ->select('id', 'name') // Only grab what we need for the dropdown
            ->get();

        // 2. Fetch all sub-categories (Global)
        // Eager loading 'category' so we can show "Men -> TopWear" in the dropdown
        $subCategories = SubCategory::with('category')->get();

        return view('vendor.discounts.create', compact('products', 'subCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDiscountRequest $request)
    {
        $vendorId = auth()->user()->id;

        // After validation..
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@store] Vendor ID: {$vendorId} is attempting to store a new discount.");

        // Grab the validated data
        $validatedData = $request->validated();

        // Force the vendor_id to be the currently logged-in user (Security!)
        $validatedData['vendor_id'] = auth()->id();

        // Create the discount
        $discount = Discount::create($validatedData);

        // log the success
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@store] Discount ID: {$discount->id} successfully created by Vendor ID: {$vendorId}.");

        return redirect()->route('vendor.discounts.index')
            ->with('success', 'Discount created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        $vendorId = auth()->id();

        // Log the request
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@edit] Vendor ID: {$vendorId} requested edit form for Discount ID: {$discount->id}");

        // Security check
        if ($discount->vendor_id !== $vendorId) {
            logger()->warning("[app\Http\Controllers\Vendor\DiscountController@edit] Unauthorized access attempt by Vendor ID: {$vendorId} on Discount ID: {$discount->id}");
            abort(403);
        }

        $products = Product::where('vendor_id', $vendorId)->where('is_active', true)->get();
        $subCategories = SubCategory::with('category')->get();

        return view('vendor.discounts.edit', compact('discount', 'products', 'subCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreDiscountRequest $request, Discount $discount)
    {
        $vendorId = auth()->id();

        // Log the initiation
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@update] Update process initiated for Discount ID: {$discount->id} by Vendor ID: {$vendorId}");

        // Security check
        if ($discount->vendor_id !== $vendorId) {
            logger()->error("[app\Http\Controllers\Vendor\DiscountController@update] Unauthorized update attempt blocked for Vendor ID: {$vendorId}");
            abort(403);
        }

        // Grab validated data from your Request Class
        $validatedData = $request->validated();

        // Log validation success
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@update] Data validated successfully for Discount: {$discount->name}");

        // Update the record
        $discount->update($validatedData);

        // Log the completion
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@update] Discount ID: {$discount->id} successfully updated.");

        return redirect()->route('vendor.discounts.index')
            ->with('success', 'Discount updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        $vendorId = auth()->id();

        // log the initiation
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@destroy] Vendor ID: {$vendorId} is attempting to delete Discount ID: {$discount->id}.");

        // Security Check: Ensure the vendor owns this discount
        if ($discount->vendor_id !== $vendorId) {
            logger()->warning("[app\Http\Controllers\Vendor\DiscountController@destroy] Unauthorized delete attempt by Vendor ID: {$vendorId} on Discount ID: {$discount->id}.");
            abort(403);
        }

        $discountName = $discount->name;
        $discount->delete();

        // log the success
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@destroy] Discount ID: {$discount->id} successfully deleted by Vendor ID: {$vendorId}.");

        return redirect()->route('vendor.discounts.index')
            ->with('success', "Discount '{$discountName}' has been deleted permanently.");
    }

    /**
     * toggle active state
     */
    public function toggle(Discount $discount)
    {
        // Ensure the vendor owns this discount (Security!)
        if ($discount->vendor_id !== auth()->id()) {
            abort(403);
        }

        // log the action
        logger()->info("[app\Http\Controllers\Vendor\DiscountController@toggle] Discount state update initiated");

        $discount->update([
            'is_active' => ! $discount->is_active,
        ]);

        $status = $discount->is_active ? 'Activated' : 'Paused';

        return back()->with('success', "Discount '{$discount->name}' has been {$status}!");
    }
}
