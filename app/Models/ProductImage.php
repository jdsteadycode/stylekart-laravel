<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    // columns to be filled
    protected $fillable = [
        "product_id",
        "image_url",
        "is_primary",
        "sort_order",
    ];

    // related product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
