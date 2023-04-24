<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrigin extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function productItems() {
        return $this->belongsToMany(ProductItem::class, 'product_relations');
    }
}
