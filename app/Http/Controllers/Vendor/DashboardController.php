<?php

// folder path
namespace App\Http\Controllers\Vendor;

// Controller class path
use App\Http\Controllers\Controller;

// Request class path
use Illuminate\Http\Request;

// Model class paths
use App\Models\ProductVariant;
use App\Models\Wallet;
// use App\Models\User;
// use App\Models\Order;
// use App\Models\Product;

// Log, DB Facade class path(s)
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /*
    show dashboard for vendor
    */
    // public function index(Request $request)
    // {
    //     // Log the action
    //     Log::info(
    //         "[app\Http\Controllers\Vendor\DashboardController@index] Vendor dashboard requested!",
    //     );

    //     // get the authenticated user
    //     $vendor = $request->user();

    //     // when no vendor authenticated!
    //     abort_if(!$vendor, 403);

    //     /***
    //      * ANALYTICS
    //      */
    //     // total revenue..
    //     $totalRevenue = DB::table('orders')
    //         ->join('order_items', 'order_items.order_id', '=', 'orders.id')
    //         ->where('order_items.vendor_id', '=', $vendor->id)
    //         ->where('orders.payment_status', '=', 'paid')
    //         ->sum(DB::raw('order_items.quantity * order_items.price'));

    //     // log the status
    //     logger()->info("{$vendor->name}'s total revenue: {$totalRevenue}");

    //     // total revenue this Year's current month..
    //     $thisMonthRevenue = DB::table('orders')
    //         ->join('order_items', 'order_items.order_id', '=', 'orders.id')
    //         ->where('order_items.vendor_id', '=', $vendor->id)
    //         ->where('orders.payment_status', '=', 'paid')
    //         ->whereMonth('orders.created_at', '=', now()->month)
    //         ->whereYear('orders.created_at', '=', now()->year)
    //         ->sum(DB::raw('order_items.quantity * order_items.price'));

    //     // log the status
    //     logger()->info("{$vendor->name}'s this month's revenue: {$thisMonthRevenue}");

    //     // get today's revenue..
    //     $todayRevenue = DB::table('orders')
    //         ->join('order_items', 'order_items.order_id', '=', 'orders.id')
    //         ->where('order_items.vendor_id', $vendor->id)
    //         ->where('orders.payment_status', 'paid')
    //         ->whereDate('orders.created_at', today())
    //         ->sum(DB::raw('order_items.quantity * order_items.price'));

    //     // log the status
    //     logger()->info("{$vendor->name}'s today's revenue: {$todayRevenue}");


    //     // get pending revenue..
    //     $pendingRevenue = DB::table('orders')
    //         ->join('order_items', 'order_items.order_id', '=', 'orders.id')
    //         ->where('order_items.vendor_id', $vendor->id)
    //         ->where('orders.payment_status', 'pending')
    //         ->where('orders.order_status', '!=', 'cancelled')   // and order shouldn't be cancelled..
    //         ->sum(DB::raw('order_items.quantity * order_items.price'));


    //     // log the status
    //     logger()->info("{$vendor->name}'s total pending revenue: {$pendingRevenue}");


    //     /**
    //      * SALES PERFORMANCE SECTION
    //      */
    //     // get total units (variants) sold..
    //     $totalUnitsSold = DB::table('orders')
    //         ->join('order_items', 'order_items.order_id', '=', 'orders.id')
    //         ->where('order_items.vendor_id', $vendor->id)
    //         ->where('orders.payment_status', 'paid')
    //         ->sum('order_items.quantity');

    //     // log the status
    //     logger()->info("Total Units Sold {$totalUnitsSold}");


    //     // variants which run out of stock..
    //     $lowStockVariants = DB::table('products')
    //         ->join('product_variants', 'product_variants.product_id', 'products.id')
    //         ->join('product_colors', 'product_colors.id', 'product_variants.color_id')
    //         ->where('products.vendor_id', $vendor->id)
    //         ->where('product_variants.stock', '<=', 3)
    //         ->select(
    //             'products.id as product_id',
    //             'products.name as product_name',
    //             'product_variants.id as variant_id',
    //             'product_variants.size as size',
    //             'product_colors.name as color'
    //         )
    //         ->get();

    //     // log the status
    //     logger()->info("Low stock variants fetched", ["status" => $lowStockVariants]);

    //     /**
    //      * OPERATIONAL SECTION
    //      */
    //     // total products.
    //     $totalProducts = $vendor->products->count();

    //     // Log the status
    //     Log::info("vendor $vendor->name and products $totalProducts fetched");

    //     // total active
    //     $totalActiveProducts = $vendor->products
    //         ->where("is_active", 1)
    //         ->count();

    //     // Log the status
    //     Log::info("Total Active Products", ["total" => $totalActiveProducts]);

    //     // total in-active
    //     $totalInActiveProducts = $vendor->products
    //         ->where("is_active", 0)
    //         ->count();

    //     // log the status
    //     Log::info("Total In-Active Products", [
    //         "total" => $totalInActiveProducts,
    //     ]);

    //     // recent products
    //     $recentProducts = $vendor->products()->with('colors.media')->latest()->limit(5)->get();

    //     // log the status
    //     Log::info("Recent Products", ["total" => $recentProducts->count()]);

    //     // get the total orders..
    //     $totalOrders = Order::whereHas('items', fn($order_item) => $order_item->where('vendor_id', $vendor->id))->count();

    //     // get total processed orders..
    //     $processingOrders = Order::whereHas('items', fn($order_item) => $order_item->where('vendor_id', $vendor->id))->where('order_status', 'processing')->count();

    //     // get total shipped orders..
    //     $shippedOrders = Order::whereHas('items', fn($order_item) => $order_item->where('vendor_id', $vendor->id))->where('order_status', 'shipped')->count();

    //     // get total shipped orders..
    //     $deliveredOrders = Order::whereHas('items', fn($order_item) => $order_item->where('vendor_id', $vendor->id))->where('order_status', 'delivered')->count();

    //     // get cancelled orders..
    //     $cancelledOrders = Order::whereHas(
    //         'items',
    //         fn($q) =>
    //         $q->where('vendor_id', $vendor->id)
    //     )
    //         ->where('order_status', 'cancelled')
    //         ->count();


    //     return view(
    //         "vendor.dashboard.index",
    //         compact([
    //             "totalRevenue",
    //             "thisMonthRevenue",
    //             "todayRevenue",
    //             "pendingRevenue",
    //             "totalUnitsSold",
    //             "lowStockVariants",
    //             // "totalProducts",
    //             // "totalActiveProducts",
    //             // "totalInActiveProducts",
    //             "recentProducts",
    //             // "totalOrders",
    //             // "processingOrders",
    //             // "shippedOrders",
    //             // "deliveredOrders",
    //             // "cancelledOrders",
    //         ]),
    //     );
    // }


    /**
     * for dashboard view (index)
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
        if (!$vendor) {
            // log the action
            logger()->error("OOPS! Not authenticated! | Terminating Dashboard request");

            // back to log-in
            return redirect()->route('login');
        }

        /*
        * today's revenue..
        */
        $todayRevenue = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.vendor_id', '=', $vendor->id)
            ->where('orders.payment_status', '=', 'paid')
            ->where('orders.order_status', '=', 'delivered')    // fix: counts earning only when order was delivered to customer && was paid
            ->where(function ($query) {
                // exclude those orders where order items are returned to vendor
                $query->where('order_items.return_status', '!=', 'received')
                    ->orWhereNull('order_items.return_status');
            })
            ->whereDate('orders.created_at', today())
            ->sum(
                DB::raw('order_items.quantity * order_items.price')
            );

        // log the status
        logger()->info("today's revenue: {$todayRevenue}");


        /*
        * this month's revenue..
        */
        $thisMonthRevenue = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.vendor_id', '=', $vendor->id)
            ->where('orders.payment_status', '=', 'paid')
            ->where('order_items.order_status', '=', 'delivered')
            ->where(function ($query) {
                // exclude those orders where order items are returned to vendor
                $query->where('order_items.return_status', '!=', 'received')
                    ->orWhereNull('order_items.return_status');
            })
            ->whereMonth('order_items.created_at', now()->month)
            ->whereYear('order_items.created_at', now()->year)
            ->sum(
                DB::raw('order_items.quantity * order_items.price')
            );

        // log the status
        logger()->info("month's revenue: {$thisMonthRevenue}");


        /*
        * orders to be readyed (i.e., processing)
        */
        $ordersToReady = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.vendor_id', '=', $vendor->id)
            ->where('orders.payment_status', '!=', 'failed')
            ->where('orders.order_status', '=', 'pending')      // fix: show pending orders for vendors
            ->distinct()
            ->count('orders.id');

        // log the status
        logger()->info("Orders to Ready: {$ordersToReady}");

        /**
         * monthly sales report
         */
        $salesResults = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.vendor_id', $vendor->id)
            ->where('orders.payment_status', 'paid')
            ->where('orders.order_status', 'delivered') // ensure all orders were delivered too
            ->where(function ($query) {
                // exclude those orders where order items are returned to vendor
                $query->where('order_items.return_status', '!=', 'received')
                    ->orWhereNull('order_items.return_status');
            })
            ->whereYear('orders.created_at', now()->year)
            ->selectRaw('MONTH(orders.created_at) as month, SUM(order_items.quantity * order_items.price) as total')
            ->groupBy('month')
            ->get();

        // 2. Create the final data for the chart (The "Human" way)
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $revenueData = [];

        for ($i = 1; $i <= 12; $i++) {
            // Look at our results: "Is there any money for month $i?"
            $found = $salesResults->where('month', $i)->first();

            // If we found it, use the total. If not, just use 0.
            $revenueData[] = $found ? $found->total : 0;
        }

        /**
         * Current Workload Breakdown (Order Statuses)
         */
        $orderStatusCounts = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.vendor_id', $vendor->id)
            ->select('orders.order_status', DB::raw('count(DISTINCT orders.id) as count'))
            ->groupBy('orders.order_status')
            ->get();

        /**
         * low stock variants..
         */
        $lowStockVariants =
            // get low stock ones..
            ProductVariant::where('stock', '<=', 5)     // extended limit to 5 from 3!
            ->with(['product', 'color.media'])
            ->whereHas('product', function ($product) use ($vendor) {
                // only of current vendor..
                $product->where('vendor_id', $vendor->id);
            })
            ->limit(5)
            ->get();

        // get wallet balance.
        $vendorWallet = Wallet::where('user_id', $vendor->id)->first();
        $walletBalance = $vendorWallet ? $vendorWallet->balance : 0.00;

        // return the view..
        return view('vendor.dashboard.index', compact(
            'todayRevenue',
            'thisMonthRevenue',
            'ordersToReady',
            'monthNames',
            'revenueData',
            'orderStatusCounts',
            'lowStockVariants',
            'walletBalance'
        ));
    }
}
