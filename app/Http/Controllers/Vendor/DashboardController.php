<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// use App\Models\Product;
// use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /*
    show dashboard for vendor
    */
    public function index(Request $request)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Vendor\DashboardController@index] Vendor dashboard requested!",
        );

        // get the authenticated user
        $vendor = $request->user();

        // when no vendor authenticated!
        abort_if(!$vendor, 403);

        // total products.
        $totalProducts = $vendor->products->count();

        // Log the status
        Log::info("vendor $vendor->name and products $totalProducts fetched");

        // total active
        $totalActiveProducts = $vendor->products
            ->where("is_active", 1)
            ->count();

        // Log the status
        Log::info("Total Active Products", ["total" => $totalActiveProducts]);

        // total in-active
        $totalInActiveProducts = $vendor->products
            ->where("is_active", 0)
            ->count();

        // log the status
        Log::info("Total In-Active Products", [
            "total" => $totalInActiveProducts,
        ]);

        // recent products
        $recentProducts = $vendor->products()->with('colors.media')->latest()->limit(5)->get();

        // log the status
        Log::info("Recent Products", ["total" => $recentProducts->count()]);

        return view(
            "vendor.dashboard.index",
            compact([
                "totalProducts",
                "totalActiveProducts",
                "totalInActiveProducts",
                "recentProducts",
            ]),
        );
    }
}
