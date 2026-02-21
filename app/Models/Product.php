<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\SubCategory;
use App\Models\ProductVariant;
use App\Models\ProductColor;
use App\Models\CartItem;
use App\Models\OrderItem;

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

    // () -> related colors
    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }

    // () -> related cartItems
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // () -> related ordered items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
