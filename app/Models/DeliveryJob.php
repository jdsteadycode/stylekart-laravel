<?php

// package path
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryJob extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_type',
        'reference_id',
        'pickup_city',
        'dropoff_city',
        'pickup_address',
        'dropoff_address',
        'status',
        'delivery_person_id',
        'earnings'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'pickup_address' => 'array',  // JSON -> PHP Native Array
        'dropoff_address' => 'array', // JSON -> PHP Native Array
        'earnings' => 'decimal:2',  // Ex: 199 -> 199.00
    ];

    /**
     * Get the delivery person assigned to this gig.
     */
    public function deliveryPerson()
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }
}
