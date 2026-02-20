<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Order;

class Address extends Model
{
    // columns to be filled
    protected $fillable = [
        "user_id",
        "name",
        "phone",
        "address_line",
        "city",
        "state",
        "pincode",
        "landmark",
        "address_type",
        "is_default",
    ];

    // () -> related user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // () -> related orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
