<?php

// folder path
namespace App\Http\Controllers\Admin;

// Controller, Request class paths
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Log Facade Class
use Illuminate\Support\Facades\Log;

// Model class paths
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\VendorProfile;
use App\Models\Wallet;

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

        // Fetch Admin Wallet Balance for the widget
        $adminWallet = Wallet::where('user_id', $admin->id)->first();
        $walletBalance = $adminWallet ? $adminWallet->balance : 0.00;

        return view(
            "admin.dashboard.index",
            compact([
                "totalCategories",
                "totalSubCategories",
                "totalApprovedVendors",
                "totalPendingVendors",
                "totalRejectedVendors",
                "recentVendors",
                "walletBalance"
            ]),
        );
    }
}
