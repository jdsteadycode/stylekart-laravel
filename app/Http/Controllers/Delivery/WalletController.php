<?php

// folder path
namespace App\Http\Controllers\Delivery;

// Controller class path
use App\Http\Controllers\Controller;

// Request class path
use Illuminate\Http\Request;

// Wallet Model class path
use App\Models\Wallet;

//
class WalletController extends Controller
{
    /**
     * Display the Delivery Person's earnings wallet and ledger.
     */
    public function index()
    {
        // Log the action
        logger()->info("[app\Http\Controllers\Delivery\WalletController@index] Delivery Person Wallet loading initiated.");

        // Get the currently authenticated delivery person
        $deliveryPerson = auth()->user();

        // Fetch their wallet, or create a 0 balance one if they are new
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $deliveryPerson->id],
            ['balance' => 0.00]
        );

        // Fetch the ledger (transactions) latest first, paginated
        $transactions = $wallet->transactions()->latest()->paginate(15);

        // Log the status
        logger()->info("Transactions loaded for delivery person.", [
            "delivery_person_id" => $deliveryPerson->id,
            "total_transactions" => $transactions->count()
        ]);

        // Send the data to the delivery view
        return view('delivery-person.wallet.index', compact('wallet', 'transactions'));
    }
}
