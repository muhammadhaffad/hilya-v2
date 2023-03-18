<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeKoko($query)
    {
        return $query->where('gender', 'koko');
    }

    public function scopeGamis($query)
    {
        return $query->where('gender', 'gamis');
    }

    public function scopeAge($query, $age)
    {
        return $query->whereIn('age', $age);
    }

    public function scopeSize($query, $size)
    {
        return $query->whereIn('size', $size);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeSoldOut($query)
    {
        return $query->where('stock', 0);
    }

    public function scopeMostExpensive($query)
    {
        return $query->orderBy('price', 'desc');
    }

    public function scopeLeastExpensive($query)
    {
        return $query->orderBy('price', 'asc');
    }

    public function productOrigins() {
        return $this->belongsToMany(ProductOrigin::class, 'product_relations');
    }
}
