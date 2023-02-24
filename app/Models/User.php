<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $guarded = ['id'];

    public function shippingAddresses() {
        return $this->hasMany(ShippingAddress::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Order::class)->ofMany(['id' => 'MIN'], fn ($q) => $q->where('status', 'cart'));
    }

    public function checkout()
    {
        return $this->hasOne(Order::class)->ofMany(['id' => 'MIN'], fn ($q) => $q->where('status', 'checkout'));
    }

    public function addressSelected()
    {
        return $this->hasOne(ShippingAddress::class)->ofMany(['id' => 'MIN'], fn ($q) => $q->where('isselect', 1));
    }

    public function primaryAddress()
    {
        return $this->hasOne(ShippingAddress::class)->ofMany(['id' => 'MIN'], fn ($q) => $q->where('type', 'primary'));
    }
}
