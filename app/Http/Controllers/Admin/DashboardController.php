<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\VendorProfile;

class DashboardController extends Controller
{
    /*
    get the admin dashboard stats
    */
    public function index(Request $request)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Admin\DashboardController@index] Admin dashboard stats requested",
        );

        // get the authenticated use
        $admin = $request->user();

        // redirect to un-authorized page
        abort_if(!$admin, 403);

        // get the categories
        $totalCategories = Category::all()->count();

        // log the status
        Log::info("Total Categories", ["total" => $totalCategories]);

        // get the sub categories
        $totalSubCategories = SubCategory::all()->count();

        // log the status
        Log::info("Total Subcategories", ["total" => $totalSubCategories]);

        // get the approved vendors
        $totalApprovedVendors = VendorProfile::where(
            "status",
            "approved",
        )->count();
        $totalPendingVendors = VendorProfile::where(
            "status",
            "pending",
        )->count();
        $totalRejectedVendors = VendorProfile::where(
            "status",
            "rejected",
        )->count();

        // log the status
        Log::info(
            "Total Approved: $totalApprovedVendors, Total Pending: $totalPendingVendors, Total Rejected: $totalRejectedVendors vendors..",
        );

        // recent vendors
        $recentVendors = User::where("role", "vendor")
            ->with("vendorProfile")
            ->get();

        return view(
            "admin.dashboard.index",
            compact([
                "totalCategories",
                "totalSubCategories",
                "totalApprovedVendors",
                "totalPendingVendors",
                "totalRejectedVendors",
                "recentVendors",
            ]),
        );
    }
}
