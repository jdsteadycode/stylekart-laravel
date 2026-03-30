<?php

// folder path
namespace App\Models;

// HasFactory trait path, Model class path
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    // use HasFactory
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];

    // () -> belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // () -> might have `n` transactions
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
