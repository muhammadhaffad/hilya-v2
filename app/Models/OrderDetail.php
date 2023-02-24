<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productDetail()
    {
        return $this->belongsTo(ProductDetail::class);
    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductDetail::class, 'id', 'id', 'product_detail_id', 'product_id');
    }
}
