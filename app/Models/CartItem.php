<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;

class CartItem extends Model
{
    // columns to be filled
    protected $fillable = ["user_id", "product_id", "variant_id", "item_qty"];

    // () -> of user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // () -> of product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // () -> of variant
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
