<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function shippings() {
        return $this->hasMany(Shipping::class);
    }

    public function scopePrimary($query)
    {
        return $query->where('type', 'primary');
    }

    public function scopeSecondary($query)
    {
        return $query->where('type', 'secondary');
    }

    public function scopeSelected($query)
    {
        return $query->where('isselect', 1);
    }
}
