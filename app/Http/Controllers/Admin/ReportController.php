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
     * Admin: Generate Global Reports
     */
    public function generate(Request $request)
    {
        // log the action
        logger()->info("[Admin\ReportController@generate] Global report initiation.");

        $request->validate([
            // Added 'vendors' and 'wallets' as new types
            'report_type' => 'required|in:delivered,returns,refunds,vendors,wallets',
            'year'        => 'required|numeric|max:' . date('Y'),
            'month'       => 'nullable|numeric|between:1,12',
            'day'         => 'nullable|numeric|between:1,31',
        ]);

        // incoming data
        $type  = $request->report_type;
        $year  = $request->year;
        $month = $request->month;
        $day   = $request->day;

        // if invalid date(s)?
        if ($year == date('Y') && $month > date('m')) {
            return back()->with('error', 'You cannot report on future months.');
        }

        // filter by date
        $readableDate = "";
        $dateFilter = function ($query) use ($request, $year, $month, $day, &$readableDate) {
            $query->whereYear('created_at', $year);
            if ($request->quarterly == '1') {
                $curM = now()->month;
                $start = ceil($curM / 3) * 3 - 2;
                $end = ceil($curM / 3) * 3;
                $query->whereMonth('created_at', '>=', $start)->whereMonth('created_at', '<=', $end);
                $readableDate = "Q" . ceil($curM / 3) . " (" . Carbon::create()->month($start)->format('M') . "-" . Carbon::create()->month($end)->format('M') . ") " . $year;
            } else {
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
                $query = OrderItem::query();
                $dateFilter($query);

                $itemsQuery = match ($type) {
                    'delivered' => $query->where('order_status', 'delivered')->whereHas('order', fn($q) => $q->where('payment_status', 'paid')),
                    'returns'   => $query->whereNotNull('return_status'),
                    'refunds'   => $query->where('return_status', 'received'),
                };

                $totalValue = (clone $itemsQuery)->sum(DB::raw('price * quantity'));
                $results = $itemsQuery->with(['product', 'order', 'vendor'])->latest()->paginate(10);
                break;

            case 'vendors':
                $results = User::where('role', 'vendor')
                    ->withCount(['soldItems as total_sales_count' => function ($query) use ($dateFilter) {
                        $query->where('order_status', 'delivered');
                        $dateFilter($query); // This filters by the date in your modal
                    }])
                    ->orderBy('total_sales_count', 'desc')
                    ->paginate(10);
                $totalValue = 0;
                break;

            case 'wallets':
                // Audit total money in system
                $results = Wallet::with('user')->orderBy('balance', 'desc')->paginate(10);
                $totalValue = Wallet::sum('balance');
                break;

            default:
                abort(404);
        }

        $stats = [
            'type_label'  => strtoupper($type),
            'total_count' => $results->total(),
            'total_value' => $totalValue,
            'date_string' => $readableDate
        ];

        // --- STEP 3: DOWNLOAD
        if ($request->has('download')) {
            $pdf = Pdf::loadView('admin.reports.pdf', [
                'results' => $results->items(), // Get all for PDF
                'stats' => $stats,
                'type' => $type
            ]);
            return $pdf->download("Stylekart_Admin_{$type}_Report.pdf");
        }

        // preview the results
        return view('admin.reports.index', compact('results', 'stats', 'type', 'year', 'month', 'day'));
    }
}
