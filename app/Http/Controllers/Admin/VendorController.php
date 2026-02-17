<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    /*
        all vendors
    */
    public function index(Request $request)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Admin\VendorController@index] All vendors fetch initiated!",
        );

        // when status
        $status = $request->query("status");
        if ($status) {
            // get vendors with status requested.
            $query = User::where("role", "vendor")->with("vendorProfile");

            // get the those vendor profile whose status matches incoming status
            $vendors = $query
                ->whereHas(
                    "vendorProfile",
                    fn($profile) => $profile->where("status", $status),
                )
                ->get();

            // Log the action
            Log::info("Vendors with status: $status fetched", [
                "total" => $vendors->count(),
            ]);
        }

        // Get all users with role vendor, eager load profile
        else {
            $vendors = User::where("role", "vendor")
                ->with("vendorProfile")
                ->get();
        }

        if (!$vendors) {
            Log::warning("No Vendor Data found!");
        }

        // log status
        Log::info("Vendors found", ["total" => $vendors->count()]);

        return view("admin.vendors.index", compact("vendors"));
    }

    /**
     * Update vendor profile status (approve/reject/pending).
     */
    public function update(Request $request, User $vendor)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Admin\VendorController@update] Vendor status update initiated",
        );

        $request->validate([
            "status" => "required|in:pending,approved,rejected",
        ]);

        // Update vendor profile
        $updated = $vendor->vendorProfile->update([
            "status" => $request->status,
        ]);
        Log::info("$vendor->name status update!", [
            "status" => (bool) $updated,
        ]);

        return redirect()
            ->route("admin.vendors.index")
            ->with(
                "success",
                "Vendor {$vendor->name} status updated to {$request->status}!",
            );
    }

    /**
     *  Show vendor details
     */
    public function show(User $vendor)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Admin\VendorController@show] Fetching vendor details initiated",
        );

        // get vendor profile withing this request early..
        $vendor->load("vendorProfile");

        // when no profile exist!
        if (!$vendor->vendorProfile) {
            return redirect()
                ->back()
                ->with("error", "No vendor Profile found!");

            // log the error
            Log::warning("No vendor Profile found");
        }

        // Log the loaded data counts for debugging
        Log::info("Vendor loaded", [
            "vendor_profile" => (bool) $vendor->vendorProfile,
        ]);

        // Pass vendor object to view
        return view("admin.vendors.show", compact("vendor"));
    }
}
