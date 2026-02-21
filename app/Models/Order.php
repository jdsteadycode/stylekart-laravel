<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Address;
use App\Models\OrderItem;

class Order extends Model
{
    // columns to be filled.
    protected $fillable = [
        "user_id",
        "order_number",
        "address_id",
        "total_amount",
    ];

    // () -> to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // () -> to address
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // () -> related ordered items in it
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    Mutators
    */
}
