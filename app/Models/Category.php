<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\SubCategory;

class Category extends Model
{
    // use SoftDeletes;

    protected $fillable = ["name"];

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    /*
    Accessors..
    */
    public function getNameAttribute($name)
    {
        // capitalize the category name whenever fetched
        return ucfirst($name);
    }
}
