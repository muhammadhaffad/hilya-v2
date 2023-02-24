<?php

namespace App\Http\Controllers\PreorderProductPage;

use App\Models\Product;
use Illuminate\Http\Request;

class PreorderProduct
{
    public static function getPreorderProducts() {
        return Product::preorder()
            ->whereHas('productDetails', function ($query) {
                $query->inStock();
            })
            ->with([
                'productBrand:id,name',
                'productImages:product_id,id,image'
            ])->withMin(
                'productDetails', 'price'
            )->withMax(
                'productDetails', 'price'
            );
    }
}
