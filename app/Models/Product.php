<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    // columns to add.
    protected $fillable = [
        'vendor_id',
        'sub_category_id',
        'name',
        'description',
        'base_price',
        'is_active',
        'brand_id',
    ];

    // () -> has a vendor
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    // () -> related to subcategory
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
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

    // () -> related wishlisted items
    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class);
    }

    // () -> discount
    public function discount()
    {
        return $this->hasOne(Discount::class);
    }

    // () -> is of brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /*
    Accessors
    */
    // () -> get total images of the product..
    public function getTotalImagesAttribute()
    {
        return $this->colors->sum(fn ($color) => $color->getMedia('color_images')->count());
    }

    // New
    /**
     * gets if live discount applied to product!
     */
    public function getActiveDiscount()
    {
        return Discount::where('is_active', true)
            ->where('product_id', $this->id)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();
    }

    /**
     *  To get SP according to above discount on product
     */
    public function getSellingPriceAttribute()
    {

        // log the action
        logger()->info("[app\Models\Product@getSellingPriceAttribute] Getting Selling Price with or without discount initiated");

        // get the discount if exists?
        $discount = $this->getActiveDiscount();

        // If no discount exists, the selling price is just the original price
        if (! $discount) {

            // log the status
            logger()->info('No discount found on this product. Keeping rates intact');

            return $this->base_price;
        }

        // Calculate based on type
        if ($discount->discount_type === 'percentage') {
            // log the action
            logger()->info('Discount exists. Discount type: Percentage');

            // discounted price
            $reduction = ($this->base_price * $discount->discount_value) / 100;

            // log the status
            logger()->info("Discount applied! | Discount value: {$reduction} on base price: {$this->base_price} | final price: {($this->base_price - $reduction)}");

            return round($this->base_price - $reduction);
        }

        // otherwise,
        // log the action
        logger()->info('Discount exists. Discount type: Value | Added discount value..');

        // log the end
        logger()->info('Getting Discounted Selling Price calcuation complete.');

        // Fixed amount logic (max(0, ...) ensures price never goes below zero)
        return round(max(0, $this->base_price - $discount->discount_value));
    }
}
