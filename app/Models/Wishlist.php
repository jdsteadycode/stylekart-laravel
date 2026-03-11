<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    // columns to be filled.
    protected $fillable = ['user_id', 'product_id', 'variant_id'];

    // () -> by customer
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // () -> for related product
    public function product()
    {
        return $this->belongsTo(Product::class)->with('variants', 'vendor.vendorProfile');
    }

    // () -> which sellable unit.
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
