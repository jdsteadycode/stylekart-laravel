<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\VendorProfile;
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
            $vendor->vendorProfile->create(["status" => "pending"]);

        // log the status
        Log::info("Vendor profile fetched", [
            "status" => (bool) $vendorProfile,
        ]);
        return view("vendor.profile.edit", compact("vendor", "vendorProfile"));
    }

    /*
        vendor profile update..
    */
    public function update(Request $request)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Vendor\VendorProfileController@update] Vendor Profile details update initiated",
        );

        $request->validate(
            [
                "name" => "required|string|max:255",
                "shop_name" => "required|string|max:255",
                "shop_address" => "required|string",
            ],
            [
                "name.required" => "Name is required.",

                "shop_name.required" => "Shop Name is required.",
                "shop_name.string" => "Shop name must be valid text.",

                "shop_address.required" => "Shop address is required.",
                "shop_address.string" => "Shop address must be valid text.",
            ],
        );

        $vendor = auth()->user();

        $updatedPersonalDetails = $vendor->update(["name" => $request->name]);

        // log the status
        Log::info("Vendor details updated", [
            "status" => (bool) $updatedPersonalDetails,
        ]);

        $updatedShopDetails = $vendor->vendorProfile()->updateOrCreate(
            ["user_id" => $vendor->id],
            [
                "shop_name" => $request->shop_name,
                "shop_address" => $request->shop_address,
                "status" => "pending",
            ],
        );

        // log the status..
        Log::info("Vendor Profile updated!", [
            "status" => (bool) $updatedShopDetails,
        ]);

        return redirect()
            ->back()
            ->with("success", "Profile submitted for approval.");
    }
}
