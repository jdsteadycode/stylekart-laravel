<?php

// folder path
namespace App\Http\Controllers\Admin;

// Controller class path
use App\Http\Controllers\Controller;

// Request class path
use Illuminate\Http\Request;

// Model class path
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wallet;
use App\Models\ProductVariant;

// Pdf Facade, DB Facade class path
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

// for date(s)
use Carbon\Carbon;


class ReportController extends Controller
{
    /**
     * Admin: Main Reporting Hub
     */
    public function index()
    {
        // log the action
        logger()->info("[Admin\ReportController@index] Admin Report Center accessed.");
        return view('admin.reports.index');
    }


    /**
     * Helper methods
     */
    //  () -> get wallet report
    private function getWalletReport()
    {
        // log the action
        logger()->info("[app\Http\Controllers\Admin\ReportController@getWalletReport] Wallet report triggered");

        // get wallet data
        $results = Wallet::with('user')
            ->orderBy('balance', 'desc')
            ->paginate(10);

        // total balance
        $totalValue = Wallet::sum('balance');

        // log the status
        logger()->info("[app\Http\Controllers\Admin\ReportController@getWalletReport] Wallet results count: " . $results->count());

        // return array
        return [
            'results' => $results,
            'totalValue' => $totalValue
        ];
    }

    // () -> get vendor report
    private function getVendorReport($dateFilter)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Admin\ReportController@getVendorReport] Vendor report triggered");

        // get data from vendor(s)
        $results = User::where('role', 'vendor')
            // only total sales
            ->withCount(['soldItems as total_sales_count' => function ($query) use ($dateFilter) {
                // where each order_item's order_status is delivered.
                $query->where('order_status', 'delivered');

                // filter it by data
                $dateFilter($query);
            }])
            // maximum first
            ->orderBy('total_sales_count', 'desc')
            // limit by 10
            ->paginate(10);

        // log the status
        logger()->info("[app\Http\Controllers\Admin\ReportController@getVendorReport] Vendor count: " . $results->count());

        // get array
        return [
            'results' => $results,
            'totalValue' => 0
        ];
    }

    // () -> get order report
    private function getOrderReport($type, $dateFilter)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Admin\ReportController@getOrderReport] Type: " . $type);

        // begin with OrderItem query.
        $query = OrderItem::query();

        // apply date filter.
        $dateFilter($query);

        // modify the query by type of report.
        $baseQuery = match ($type) {
            'delivered' => $query->where('order_status', 'delivered')
                ->whereHas('order', fn($q) => $q->where('payment_status', 'paid')),

            'returns' => $query->whereNotNull('return_status'),

            'refunds' => $query->where('return_status', 'received'),
        };

        // calculate total-value
        $totalValue = (clone $baseQuery)->sum(DB::raw('price * quantity'));

        // get results.
        $results = (clone $baseQuery)
            ->with(['product', 'order', 'vendor'])
            ->latest()
            ->paginate(10);

        // log the status
        logger()->info("[app\Http\Controllers\Admin\ReportController@getOrderReport] Count: " . $results->count());

        // get array.
        return [
            'results' => $results,
            'totalValue' => $totalValue,
            'baseQuery' => $baseQuery // ⚠️ IMPORTANT (for PDF later)
        ];
    }




    /**
     * Admin: Generate Global Reports
     */
    public function generate(Request $request)
    {
        // log the action
        logger()->info("[Admin\ReportController@generate] Global report initiation.");

        // validate the data incoming..
        $request->validate([
            // Added 'vendors' and 'wallets' as new types
            'report_type' => 'required|in:delivered,returns,refunds,vendors,wallets',
            'year'        => 'required|numeric|max:' . date('Y'),
            'month'       => 'nullable|numeric|between:1,12',
            'day'         => 'nullable|numeric|between:1,31',
        ]);

        // incoming data..
        $type  = $request->report_type;
        $year  = $request->year;
        $month = $request->month;
        $day   = $request->day;

        // log the type of report to generate..
        logger()->info("Report in Generation: {$type}");

        // if date (year or day or month) is in future.
        if ($year == date('Y') && $month > date('m')) {

            // back with error
            return back()->with('error', 'You cannot report on future months.');
        }

        $readableDate = "";

        // () -> filter by date
        $dateFilter = function ($query) use ($request, $year, $month, $day, &$readableDate) {

            // filter by given year
            $query->whereYear('created_at', $year);

            // if quarterly filter is requested for the report selected?
            if ($request->quarterly == '1') {
                $curM = now()->month;
                $start = ceil($curM / 3) * 3 - 2;
                $end = ceil($curM / 3) * 3;
                $query->whereMonth('created_at', '>=', $start)->whereMonth('created_at', '<=', $end);
                $readableDate = "Q" . ceil($curM / 3) . " (" . Carbon::create()->month($start)->format('M') . "-" . Carbon::create()->month($end)->format('M') . ") " . $year;
            }

            // otherwise filter by given date
            else {
                if ($month) $query->whereMonth('created_at', $month);
                if ($day)   $query->whereDay('created_at', $day);
                $readableDate = Carbon::create($year, $month ?? 1, $day ?? 1)->format($day ? 'd M Y' : ($month ? 'M Y' : 'Y'));
            }
        };


        // Use match to decide which model to query
        switch ($type) {
            case 'delivered':
            case 'returns':
            case 'refunds':
                // get order data
                $orderData = $this->getOrderReport($type, $dateFilter);

                // results, totalValue, baseQuery
                $results = $orderData['results'];
                $totalValue = $orderData['totalValue'];
                $baseQuery = $orderData['baseQuery'];
                break;

            // when vendor report is accessed.
            case 'vendors':

                // get vendor data
                $vendorData = $this->getVendorReport($dateFilter);

                // results
                $results = $vendorData['results'];
                $totalValue = $vendorData['totalValue'];
                break;

            // when wallet report selected.
            case 'wallets':

                // get wallet data
                $walletData = $this->getWalletReport();

                // destructure the code
                $results = $walletData['results'];
                $totalValue = $walletData['totalValue'];
                break;

            // default (fallback option)
            default:
                abort(404);
        }

        // for reports view
        $stats = [
            'type_label'  => strtoupper($type),
            'total_count' => $results->total(),
            'total_value' => $totalValue,
            'date_string' => $readableDate
        ];

        // log the status
        logger()->info("Preview results count: " . $results->count());
        logger()->info("Is paginator? " . (method_exists($results, 'links') ? 'YES' : 'NO'));


        // if download is requested?
        if ($request->has('download')) {

            // log the report type
            logger()->info("PDF download triggered for type: " . $type);

            // check report type
            switch ($type) {
                // when type of report asked is either of (delivered || returns || refunds)
                case 'delivered':
                case 'returns':
                case 'refunds':
                    // get results.
                    $fullResults = (clone $baseQuery)->with(['product', 'order', 'vendor'])->latest()->get();
                    break;

                // if vendor report
                case 'vendors':
                    $fullResults = User::where('role', 'vendor')
                        ->withCount(['soldItems as total_sales_count' => function ($query) use ($dateFilter) {
                            $query->where('order_status', 'delivered');
                            $dateFilter($query);
                        }])
                        ->orderBy('total_sales_count', 'desc')
                        ->get();
                    break;

                // if wallets report
                case 'wallets':
                    $fullResults = Wallet::with('user')->orderBy('balance', 'desc')->get();
                    break;

                default:
                    $fullResults = collect();
            }

            // log the status
            logger()->info("PDF full dataset count: " . $fullResults->count());

            // load the data in pdf
            $pdf = Pdf::loadView('admin.reports.pdf', [
                'results' => $fullResults,
                'stats' => $stats,
                'type' => $type
            ]);

            // download it
            return $pdf->download("Stylekart_Admin_{$type}_Report.pdf");
        }

        // preview the results
        return view('admin.reports.index', compact('results', 'stats', 'type', 'year', 'month', 'day'));
    }


    /**
     * Admin: Customer Wallet transaction details
     */
    public function walletDetails(User $user)
    {
        // log the action.
        logger()->info("[app\Http\Controllers\Admin\ReportController@walletDetails] Wallet details view initiated!");

        // log the status
        logger()->info("Wallet details accessed for user: " . $user->id);

        // get the customer's wallet
        $wallet = $user->wallet;

        // when no wallet found?
        if (!$wallet) {
            // log the warning
            logger()->warning("No wallet found for user: " . $user->id);

            // back safely with error
            return back()->with('error', 'Wallet not found for this user.');
        }

        // get the related transactions..
        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(10);

        // log the status
        logger()->info("Transaction count: " . $transactions->count());

        // get the view with data.
        return view('admin.reports.wallet-details', compact('user', 'wallet', 'transactions'));
    }
}
