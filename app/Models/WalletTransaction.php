<?php

// folder path
namespace App\Models;

// HasFactory trait path, Model class path
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    // columns to be filled
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
        'reference_type',
        'reference_id'
    ];

    // () -> belongs to wallet
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    // The polymorphic relationship bridge
    public function reference()
    {
        return $this->morphTo();
    }
}
