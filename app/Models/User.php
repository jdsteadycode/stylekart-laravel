<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // added role column to be filled..
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // () -> wishlisted items..
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    // () -> vendor profile
    public function vendorProfile()
    {
        return $this->hasOne(VendorProfile::class);
    }

    // () -> as vendor, can have many products
    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
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
        return $this->hasMany(OrderItem::class, 'vendor_id');
    }

    // () -> related delivery_profile
    public function deliveryProfile()
    {
        return $this->hasOne(DeliveryProfile::class);
    }

    // () -> related assigned orders (as delivery personnel)
    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'delivery_person_id');
    }
}
