<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['custom_properties' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }

    public function productItems()
    {
        return $this->hasManyThrough(ProductItem::class, OrderItem::class, 'order_id', 'id', 'id', 'product_item_id');
    }

    public function scopeCode($query, $code)
    {
        return $query->where($this->getTable() . '.code', $code);
    }

    public function scopeCart($query)
    {
        return $query->where($this->getTable() . '.status', 'cart');
    }

    public function scopeCheckout($query)
    {
        return $query->where($this->getTable() . '.status', 'checkout');
    }

    public function scopeWithoutStatus($query, $status)
    {
        return $query->whereNotIn($this->getTable() . '.status', $status);
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->whereIn($this->getTable() . '.status', $status);
    }
}
