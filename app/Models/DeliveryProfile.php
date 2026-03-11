<?php

namespace App\Models;

// grab User Class path
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class DeliveryProfile extends Model
{

    // columns to be filled..
    protected $fillable = [
        "user_id",
        "vehicle_number",
        "vehicle_type",
        "phone_number",
        "is_available",
        "current_location"
    ];

    // of user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
