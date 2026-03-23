<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorProfileRequest;
use Illuminate\Support\Facades\Log;

class VendorProfileController extends Controller
{
    /*
        get the vendor profile
    */
    public function edit()
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\VendorProfileController@edit] Vendor Profile details edit initiated",
        );
        $vendor = auth()->user();

        // get profile or make one
        $vendorProfile =
            $vendor->vendorProfile ??
            $vendor->vendorProfile->create(['status' => 'pending']);

        // log the status
        Log::info('Vendor profile fetched', [
            'status' => (bool) $vendorProfile,
        ]);

        return view('vendor.profile.edit', compact('vendor', 'vendorProfile'));
    }

    /*
        vendor profile update..
    */
    public function update(VendorProfileRequest $request)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\VendorProfileController@update] Vendor Profile details update initiated",
        );

        $validated = $request->validated();

        // authenticated vendor..
        $vendor = auth()->user();

        // update the details..
        $updatedPersonalDetails = $vendor->update($validated);

        // log the status
        Log::info('Vendor details updated', [
            'status' => (bool) $updatedPersonalDetails,
        ]);

        $updatedShopDetails = $vendor->vendorProfile()->updateOrCreate(
            ['user_id' => $vendor->id],
            [
                'shop_name' => $request->shop_name,
                'shop_address' => $request->shop_address,
                'status' => 'pending',
            ],
        );

        // log the status..
        Log::info('Vendor Profile updated!', [
            'status' => (bool) $updatedShopDetails,
        ]);

        // back with success
        return redirect()
            ->back()
            ->with('success', 'Profile submitted for approval.');
    }
}
