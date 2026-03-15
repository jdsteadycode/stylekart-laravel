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

    /**
     * Calculate the selling price for this specific variant.
     */
    public function getSellingPriceAttribute()
    {
        // 1. Get the parent product's active discount
        $discount = $this->product->getActiveDiscount();

        // 2. Use this variant's price
        $price = $this->price;

        // 3. If no discount exists, return original price
        if (!$discount) {
            return $price;
        }

        // 4. Calculate based on the discount type
        if ($discount->discount_type === 'percentage') {
            return round($price - ($price * ($discount->discount_value / 100)));
        }

        // 5. otherwise calculate discount by value
        return round(max(0, $price - $discount->discount_value));
    }
}
