<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Product;

class PreorderProduct
{
    public static function getPreorderProducts()
    {
        return Product::preorder()->whereHas('productItems', function ($query) {
                $query->inStock();
            })
            ->with([
                'productBrand:id,name',
                'productImages:product_id,id,image'
            ])->withMin(
                'productItems',
                'price'
            )->withMax(
                'productItems',
                'price'
            );
    }
}
