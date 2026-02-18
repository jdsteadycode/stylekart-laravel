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

    /*
    Mutatots
    */
    // () -> standardize the color values..
    public function setColorAttribute($colorValue)
    {
        // trim and standardize the color values
        $this->attributes["color"] = trim(strtolower($colorValue));
    }
}
