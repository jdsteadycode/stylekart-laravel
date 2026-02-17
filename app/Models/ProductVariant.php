<?php

namespace App\Models;
use App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    // columns to be filled..
    protected $fillable = [
        "product_id",
        "color",
        "size",
        "price",
        "stock",
        "sku",
    ];

    // () -> related product..
    public function product()
    {
        $this->belongsTo(Product::class);
    }
}
