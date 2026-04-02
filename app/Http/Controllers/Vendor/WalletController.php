<?php

// folder path.
namespace App\Http\Controllers\Vendor;

// Controller class path.
use App\Http\Controllers\Controller;

// Request class path.
use Illuminate\Http\Request;

// Wallet model class path.
use App\Models\Wallet;

class WalletController extends Controller
{
    /**
     * Display the Vendor's earnings wallet and ledger.
     */
    public function index()
    {
        // Log the action
        logger()->info("[app\Http\Controllers\Vendor\WalletController@index] Vendor Wallet loading initiated.");

        // Get the currently authenticated vendor
        $vendor = auth()->user();

        // get their wallet, or create a 0 balance one if they are a new vendor
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $vendor->id],
            ['balance' => 0.00]
        );

        // Fetch the ledger (transactions) latest first, paginated
        $transactions = $wallet->transactions()->latest()->paginate(15);

        // Log the status
        logger()->info("Transactions loaded for vendor.", [
            "vendor_id" => $vendor->id,
            "total_transactions" => $transactions->count()
        ]);

        // Send the data to the vendor view
        return view('vendor.wallet.index', compact('wallet', 'transactions'));
    }
}
