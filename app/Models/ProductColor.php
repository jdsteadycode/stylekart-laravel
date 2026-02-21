<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

// grab spatie package utilties..
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductColor extends Model implements HasMedia
{
    // allow to interact with media
    use InteractsWithMedia;

    // columns to filled
    protected $fillable = ["product_id", "name"];

    // () -> related product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // () -> related variants i.e., one color can found in multiple variants..
    public function variants()
    {
        return $this->hasMany(ProductColor::class);
    }

    // () -> create collection
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection("color_images")->useDisk("public");
    }

    /*
    Mutators
    */
    // () -> standardize the color values..
    public function setColorAttribute($colorValue)
    {
        // trim and standardize the color values
        $this->attributes["color"] = trim(strtolower($colorValue));
    }
}
