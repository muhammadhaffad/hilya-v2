<?php

namespace App\Http\Controllers\PreorderProductPage;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductSearch
{
    public static function search($keyword)
    {
        $columns = [
            /**`table`.`column` */
            'products.name', 
            'products.description', 
            'products.ispromo', 
            'product_brands.name',
            'product_details.gender',
            'product_details.age',
            'product_details.color',
            'product_details.fabric',
            'product_details.model'
        ];
        return Product::preorder()->join('product_brands', 'product_brands.id', 'products.product_brand_id')
        ->join('product_details', 'product_details.product_id', 'products.id')
        ->join('product_images', 'product_images.product_id', 'products.id')
        ->distinct()
        ->where(function ($query) use ($keyword, $columns) {
            foreach ($columns as $key => $column) {
                if ($key == 0) {
                    $query->where($column, 'LIKE', '%'. $keyword .'%');
                } elseif ($column == 'products.ispromo') {
                    if ($keyword === 'promo') 
                        $query->orWhere('ispromo', 1);
                    else
                        $query;
                } else {
                    $query->orWhere($column, 'LIKE', '%'. $keyword .'%');
                }
            }
        })
        ->where('product_details.stock', '>', 0)
        ->withMin('productDetails', 'price')
        ->withMax('productDetails', 'price')
        ->with(['productBrand:id,name', 'productImages:product_id,image']);
    }
}
