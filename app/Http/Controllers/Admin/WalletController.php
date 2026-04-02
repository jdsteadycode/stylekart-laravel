<?php

// folder path
namespace App\Http\Controllers\Admin;

// Controller class path
use App\Http\Controllers\Controller;

// Request class path
use Illuminate\Http\Request;

// Wallet model class path
use App\Models\Wallet;

class WalletController extends Controller
{
    /**
     * Display the Admin's master commission wallet and ledger.
     */
    public function index()
    {
        // log the action.
        logger()->info("[app\Http\Controllers\Admin\WalletController@index] Wallet loading initiated.");

        // Get the currently authenticated admin user
        $admin = auth()->user();

        // get the existing wallet of admin or make one.
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $admin->id],
            ['balance' => 0.00]
        );

        // get the transactions associated and paginate them for better management and less resource consumption.
        $transactions = $wallet->transactions()->latest()->paginate(15);

        // log the status
        logger()->info("Transactions loaded for admin.", [
            "total" => $transactions->count()
        ]);

        // Send the data to the admin view
        return view('admin.wallet.index', compact('wallet', 'transactions'));
    }
}
