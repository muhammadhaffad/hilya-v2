<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function shippingAddress() {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function scopeService($query, $service) 
    {
        return $query->where('service', $service);
    }
}
