<?php

namespace App\Http\Controllers\BrandProductPage;

use App\Models\Product;

class ProductSearch
{
    public static function search($keyword, $brand) {
        $columns = [
            'products.name', 
            'products.description', 
            'products.availability',
            'products.ispromo', 
            'product_items.gender',
            'product_items.age',
            'product_items.color',
            'product_items.fabric',
            'product_items.model'
        ];
        return Product::join('product_brands', 'product_brands.id', 'products.product_brand_id')
        ->join('product_items', 'product_items.product_id', 'products.id')
        ->join('product_images', 'product_images.product_id', 'products.id')
        ->distinct()
        ->where(function ($query) use ($keyword, $columns) {
            foreach ($columns as $key => $column) {
                if ($key == 0) {
                    $query->where($column, 'LIKE', '%'. $keyword .'%');
                } elseif ($column == 'products.ispromo') {
                    if ($keyword === 'promo') 
                        $query->promo();
                    else
                        $query;
                } else {
                    $query->orWhere($column, 'LIKE', '%'. $keyword .'%');
                }
            }
        })
        ->where('product_items.stock', '>', 0)
        ->where('product_brands.slug', $brand->slug)
        ->withMin('productItems', 'price')
        ->withMax('productItems', 'price')
        ->with(['productBrand:id,name', 'productImages:product_id,image']);
    }
}
