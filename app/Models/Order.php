<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // columns to be filled.
    protected $fillable = [
        'user_id',
        'order_number',
        'address_id',
        'total_amount',
        'order_status',
        'payment_mode',
        'payment_status',
        'delivery_person_id',
        'delivery_otp',
    ];

    // () -> to user (customer)
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

    // () -> related delivery personnel
    public function deliveryPerson()
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }

    /***
     * update the status
     */
    public function updateStatus()
    {
        // .
        $items = $this->items;

        // when all items are cancelled
        if ($items->every(fn ($item) => $item->order_status === 'cancelled')) {
            $this->order_status = 'cancelled';
        }

        // when all items are delivered
        elseif ($items->every(fn ($item) => $item->order_status === 'delivered')) {
            $this->order_status = 'delivered';
        }

        // from all is there items out for delivery
        elseif ($items->every(fn ($i) => in_array($i->order_status, ['out_for_delivery', 'delivered']))) {
            $this->order_status = 'out_for_delivery';
        }

        // from all is there items with shipped
        elseif ($items->every(fn ($item) => in_array($item->order_status, ['shipped', 'delivered']))) {
            $this->order_status = 'shipped';
        }

        // from all is there items with ready_for_pickup
        elseif ($items->every(fn ($item) => in_array($item->order_status, ['ready_for_pickup', 'shipped', 'delivered']))) {
            $this->order_status = 'ready_for_pickup';
        }

        // from all is there items with processing
        elseif ($items->every(fn ($item) => in_array($item->order_status, ['processing', 'ready_for_pickup', 'shipped', 'delivered']))) {
            $this->order_status = 'processing';
        }

        // otherwise all are at pending
        else {
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

    /**
     * check if all items are ready for handing over to delivery personnel!
     */
    public function isConsolidated(): bool
    {
        // get items of order.
        $items = $this->items;

        // check if every item is ready to pickup?
        if ($items->every(fn ($item) => $item->order_status === 'ready_for_pickup')) {
            return true;
        }

        return false;
    }

    /***
     * for customer high level order status view
     */
    public function getCustomerStatusAttribute(): string
    {
        // If status 'ready_for_pickup' then, 'processing' to be shown
        if ($this->order_status === 'ready_for_pickup') {
            return 'processing';
        }

        return $this->order_status;
    }
}
