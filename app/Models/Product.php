<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\SubCategory;
use App\Models\ProductVariant;
use App\Models\ProductImage;

class Product extends Model
{
    use SoftDeletes;

    // columns to add.
    protected $fillable = [
        "vendor_id",
        "sub_category_id",
        "name",
        "description",
        "base_price",
        "is_active",
    ];

    // () -> has a vendor
    public function vendor()
    {
        return $this->belongsTo(User::class, "vendor_id");
    }

    // () -> related to subcategory
    public function subCategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    // () -> related to variants
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // () -> related images..
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
