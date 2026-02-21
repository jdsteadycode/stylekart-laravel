<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class VendorProfile extends Model
{
    // columns to be filled..
    protected $fillable = [
        "user_id",
        "shop_name",
        "shop_address",
        "phone",
        "status",
        "rejection_reason",
    ];

    // () -> related user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
