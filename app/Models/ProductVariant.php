<?php

namespace App\Models;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\OrderItem;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    // columns to be filled..
    protected $fillable = [
        "product_id",
        "color_id",
        "size",
        "price",
        "stock",
        "sku",
    ];

    // () -> related product..
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // () -> related color..
    public function color()
    {
        return $this->belongsTo(ProductColor::class);
    }

    // () -> related cart-items
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // () -> related ordered items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    Mutatots
    */
    // () -> standardize the color values..
    // public function setColorAttribute($colorValue)
    // {
    //     // trim and standardize the color values
    //     $this->attributes["color"] = trim(strtolower($colorValue));
    // }
}
