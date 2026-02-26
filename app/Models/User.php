<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Product;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "name",
        "email",
        "password",
        "role", // added role column to be filled..
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    // () -> vendor profile
    public function vendorProfile()
    {
        return $this->hasOne(VendorProfile::class);
    }

    // () -> as vendor, can have many products
    public function products()
    {
        return $this->hasMany(Product::class, "vendor_id");
    }

    // () -> related addresses.
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // () -> relate cart items
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // () -> customer related orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // () -> related sold items..
    public function soldItems()
    {
        return $this->hasMany(OrderItem::class, "vendor_id");
    }
}
