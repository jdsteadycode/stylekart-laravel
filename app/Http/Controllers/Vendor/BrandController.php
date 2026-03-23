<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a list of your fashion labels
     */
    public function index()
    {
        // get all available brands.
        $brands = Brand::where('vendor_id', auth()->id())
            ->latest()
            ->get();

        return view('vendor.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new label
     */
    public function create()
    {
        return view('vendor.brands.create');
    }

    /**
     * store new brand
     */
    public function store(Request $request)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\BrandController@store] Brand creation initiated");

        // validation
        $request->validate([
            'name' => 'required|unique:brands,name|max:100',
            'logo' => 'nullable|image|max:2048',
            'description' => 'nullable|string|max:500',
        ]);

        // get current vendor
        $user = auth()->user();

        // create the brand
        $brand = Brand::create([
            'vendor_id' => $user->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name), // auto add slug according to name of brand
            'description' => $request->description,
            'is_active' => true,
        ]);

        // handle spatie logo upload
        if ($request->hasFile('logo')) {
            $brand->addMediaFromRequest('logo')
                ->toMediaCollection('brand_logos');

            // update the logo path in table (as a flag/reference)
            $brand->update(['logo' => $brand->getFirstMediaUrl('brand_logos')]);

            // log the status
            logger()->info("Brand logo uploaded via Spatie for Brand ID: {$brand->id}");
        }

        // log the success
        logger()->info("Brand: {$brand->name} created successfully by Vendor ID: {$user->id}");

        return redirect()->route('vendor.brands.index')
            ->with('success', 'Brand created successfully!');
    }

    /**
     * show edit form
     */
    public function edit(Brand $brand)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\BrandController@edit] Editing Brand ID: {$brand->id}");

        // security: check if vendor owns this brand
        if ($brand->vendor_id !== auth()->id()) {
            logger()->alert('Unauthorized access attempt by Vendor ID: '.auth()->id().' on Brand ID: '.$brand->id);
            abort(403);
        }

        return view('vendor.brands.edit', compact('brand'));
    }

    /**
     * update existing brand
     */
    public function update(Request $request, Brand $brand)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\BrandController@update] Updating Brand ID: {$brand->id}");

        // security check
        if ($brand->vendor_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized action!');
        }

        // validation (ignore current brand id for unique check)
        $request->validate([
            'name' => 'required|max:100|unique:brands,name,'.$brand->id,
            'logo' => 'nullable|image|max:2048',
            'description' => 'nullable|string|max:500',
        ]);

        // update details
        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        // handle spatie logo replacement
        if ($request->hasFile('logo')) {
            // singleFile() in model ensures the old one is deleted automatically
            $brand->addMediaFromRequest('logo')
                ->toMediaCollection('brand_logos');

            // update the reference column
            $brand->update(['logo' => $brand->getFirstMediaUrl('brand_logos')]);

            // log status
            logger()->info("Brand logo replaced for Brand ID: {$brand->id}");
        }

        // log success
        logger()->info("Brand: {$brand->name} updated successfully by Vendor ID: ".auth()->id());

        return redirect()->route('vendor.brands.index')
            ->with('success', 'Brand details updated!');
    }

    /**
     * delete brand
     */
    public function destroy(Brand $brand)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\BrandController@destroy] Deleting Brand ID: {$brand->id}");

        // security check
        if ($brand->vendor_id !== auth()->id()) {
            logger()->alert('Unauthorized delete attempt by Vendor ID: '.auth()->id());

            return back()->with('error', 'Unauthorized action!');
        }

        // save name for the log/message before deleting
        $brandName = $brand->name;

        // Spatie will automatically clean up the physical logo files
        $brand->delete();

        // log success
        logger()->info("Brand: {$brandName} deleted successfully by Vendor ID: ".auth()->id());

        return redirect()->route('vendor.brands.index')
            ->with('success', "Brand '{$brandName}' removed successfully!");
    }
}
