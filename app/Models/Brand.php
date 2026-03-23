<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use InteractsWithMedia;

    // columns to be filled
    protected $fillable = [
        'vendor_id',
        'name',
        'slug',
        'logo',
        'description',
        'is_active',
    ];

    /**
     * Relationship: The Vendor who owns this label
     */
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Relationship: Products carrying this brand
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('brand_logos')
            ->singleFile(); // A fashion brand only needs ONE main logo
    }
}
