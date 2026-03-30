<?php

// folder path
namespace App\Http\Controllers\Vendor;

// Request class path
use Illuminate\Http\Request;

// Models class path
use App\Models\OrderItem;

// Controller class path
use App\Http\Controllers\Controller;

// Pdf facade path
use Barryvdh\DomPDF\Facade\Pdf;

// for date
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Vendor: Main Reporting Page (Filter UI)
     */
    public function index()
    {
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\ReportController@vendorIndex] Vendor Report Center accessed.");

        return view('vendor.reports.index');
    }

    /**
     * Vendor: Generate/Preview Report Data
     */
    public function generate(Request $request)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Vendor\ReportController@generate] Initiation for Vendor: " . auth()->id());

        // simple validation
        $request->validate([
            'report_type' => 'required|in:delivered,returns,refunds',
            'year'        => 'required|numeric|max:' . date('Y'),
            'month'       => 'nullable|numeric|between:1,12',
            'day'         => 'nullable|numeric|between:1,31',
        ]);

        // details and inputs
        $vendorId = auth()->id();
        $type     = $request->report_type;
        $year     = $request->year;
        $month    = $request->month;
        $day      = $request->day;

        // Date safety check
        if ($year == date('Y') && $month > date('m')) {
            return back()->with('error', 'You cannot report on future months.');
        }

        // get order items
        $query = OrderItem::where('vendor_id', $vendorId)->whereYear('created_at', $year);


        // If quaterly report asksed
        if ($request->has('quarterly')) {
            $curM = now()->month; // For example: It is currently March (Month 3)

            // Standard 3-month blocks
            if ($curM <= 3) {
                $start = 1;
                $end = 3;   //  Jan - Mar
            } elseif ($curM <= 6) {
                $start = 4;
                $end = 6;   //  Apr - Jun
            } elseif ($curM <= 9) {
                $start = 7;
                $end = 9;   // Jul - Sep
            } else {
                $start = 10;
                $end = 12; // Oct - Dec
            }

            // filter based on start and end month
            $query->whereMonth('created_at', '>=', $start)
                ->whereMonth('created_at', '<=', $end);

            // Calculate the Quarter Number (1, 2, 3, or 4)
            $qNumber = ceil($curM / 3);

            // date
            $readableDate = "Q{$qNumber} Report: " .
                Carbon::create()->month($start)->format('M') . " - " .
                Carbon::create()->month($end)->format('M') . " " . $year;
        }

        // otherwise, simple preview based report
        else {
            // Standard Filtering (Existing Functionality)
            if ($month) $query->whereMonth('created_at', $month);
            if ($day)   $query->whereDay('created_at', $day);

            $readableDate = Carbon::create($year, $month ?? 1, $day ?? 1)
                ->format($day ? 'd M Y' : ($month ? 'M Y' : 'Y'));
        }

        // Initial report query (Match remains untouched and safe)
        $itemsQuery = match ($type) {
            'delivered' => $query->where('order_status', 'delivered')
                ->whereHas('order', function ($orderQuery) {
                    $orderQuery->where('payment_status', 'paid');
                })
                ->where(function ($q) {
                    $q->whereNull('return_status')
                        ->orWhere('return_status', '!=', 'received');
                }),
            'returns'   => $query->whereNotNull('return_status'),
            'refunds'   => $query->where('return_status', 'received'),
            default     => abort(404)
        };

        // save the total for previewing the result
        $totalValue = (clone $itemsQuery)->sum(DB::raw('price * quantity'));

        // get report result with relationship methods as well
        $results = (clone $itemsQuery)->with(['product', 'order'])->latest()->paginate(6)->withQueryString();

        // log the status
        logger()->info("Report Results Found: {$results->count()}");

        $stats = [
            'type_label'  => str_replace('_', ' ', ucwords($type, '_')),
            'total_count' => $results->total(),
            'total_value' => $totalValue,
            'date_string' => $readableDate
        ];

        // if download
        if ($request->has('download')) {
            $allResults = ($itemsQuery)->with(['product', 'order'])->latest()->get();

            // log the status
            logger()->info("Total: {$allResults->count()}");

            // return the pdf view
            return \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'vendor.reports.pdf',
                ['results' => $allResults, 'stats' => $stats, 'type' => $type, 'year' => $year, 'month' => $month, 'day' => $day]
            )
                ->download("Stylekart_{$type}_Report.pdf");
        }

        // for previewing..
        return view('vendor.reports.index', compact('results', 'stats', 'type', 'year', 'month', 'day'));
    }
}
