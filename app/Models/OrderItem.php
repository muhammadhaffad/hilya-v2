<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductItem::class, 'id', 'id', 'product_item_id', 'product_id');
    }
}
