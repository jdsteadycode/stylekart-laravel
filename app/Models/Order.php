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
        "order_status",
        "payment_mode",
        "payment_status"
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


    /***
     * update the status
     */
    public function updateStatus()
    {
        //.
        $items = $this->items;

        // when all items are cancelled
        if ($items->every(fn($item) => $item->order_status === 'cancelled')) {
            $this->order_status = 'cancelled';
        }

        // when all items are delivered
        else if ($items->every(fn($item) => $item->order_status === 'delivered')) {
            $this->order_status = 'delivered';
        }

        // when all items are shipped or delivered
        else if ($items->every(fn($item) => in_array($item->order_status, ['shipped', 'delivered']))) {
            $this->order_status = 'shipped';
        }

        // when some are processing, shipped, delivered..
        else if ($items->every(fn($item) => in_array($item->order_status, ['processing', 'shipped', 'delivered']))) {
            $this->order_status = 'processing';
        } else {
            $this->order_status = 'pending';
        }

        // update the status
        return $this->save();
    }

    /***
     * check if stock was reduced before?
     */
    public function wasStockReduced()
    {
        // i.e., if order was successful -> stock was reduced!
        return $this->payment_mode === 'cod' || $this->payment_status === 'paid';
    }
}
