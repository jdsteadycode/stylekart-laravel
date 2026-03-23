<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    // columns to be filled.
    protected $fillable = [
        'vendor_id',
        'name',
        'discount_type',
        'discount_value',
        'product_id',
        'sub_category_id',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
    ];

    // () -> vendor who defined this discount
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    // () -> to which thing discount applies (a whole product)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // () -> to which thing discount applies (a sub-category)
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
