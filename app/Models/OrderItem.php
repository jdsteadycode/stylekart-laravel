<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;

class OrderItem extends Model
{
    // columns to be filled..
    protected $fillable = [
        "order_id",
        "product_id",
        "variant_id",
        "vendor_id",
        "quantity",
        "price",
        "order_status",
        "payment_mode",
        "payment_status",
    ];

    // () -> each item is related to order.
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // () -> each item is related to product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // () -> each item is related to variant
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // each item is related to vendor / seller
    public function vendor()
    {
        return $this->belongsTo(User::class, "vendor_id");
    }
}
