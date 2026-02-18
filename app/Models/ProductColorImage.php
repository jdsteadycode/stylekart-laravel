<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

// grab spatie package utilties..
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductColorImage extends Model implements HasMedia
{
    // allow to interact with media
    use InteractsWithMedia;

    // columns to filled
    protected $fillable = ["product_id", "color"];

    // () -> related product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // () -> create collection
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection("color_images");
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
