<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $set = false;
    protected $guarded = ['id'];
    protected $appends = ['link'];
    
    public function productBrand() {
        return $this->belongsTo(ProductBrand::class);
    }

    public function productImages() {
        return $this->hasMany(ProductImage::class);
    }

    public function productImage() {
        return $this->hasOne(ProductImage::class)->ofMany('id', 'MIN');
    }

    public function productItems() {
        return $this->hasMany(ProductItem::class);
    }
    
    public function getLinkAttribute() {
        return url("/products/{$this->id}");
    }

    public function scopeWithBrandName($query, $brandName)
    {
        return $query->join('product_brands', 'products.product_brand_id', 'product_brands.id')
                     ->where('product_brands.name', $brandName)
                     ->select('products.*');
    }

    public function scopeWithBrand($query, $brandId)
    {
        return $query->whereIn('product_brand_id', $brandId);
    }

    public function scopePreorder($query)
    {
        return $query->where('availability', 'pre-order');
    }
    
    public function scopeReady($query)
    {
        return $query->where('availability', 'ready');
    }

    public function scopePromo($query)
    {
        return $query->where('ispromo', 1);
    }
}
