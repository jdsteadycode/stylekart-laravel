<?php

// folder path
namespace App\Http\Controllers\Customer;

// Controller, Requet class paths..
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Wallet Model class path
use App\Models\Wallet;

// UDC - WalletController inheriting Controller Class
class WalletController extends Controller
{
    /**
     * Display the customer's wallet balance and transaction history.
     */
    public function index()
    {
        // Get the currently authenticated customer
        $customer = auth()->user();

        // The Safety Net: Fetch their wallet, or just make ₹0.00 one if it doesn't exist
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $customer->id],
            ['balance' => 0.00]
        );

        // Fetch the ledger (transactions) ensuring latest ones on top, also data size remains short and response is faster..
        $transactions = $wallet->transactions()->latest()->paginate(10);

        // Send the data to the view
        return view('customer.wallet.index', compact('wallet', 'transactions'));
    }
}
