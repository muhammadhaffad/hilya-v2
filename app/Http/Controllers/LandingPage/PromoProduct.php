<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Product;
use Illuminate\Http\Request;

class PromoProduct
{
    public static function getPromoProducts()
    {
        return Product::promo()->whereHas('productDetails', function ($query) {
                $query->inStock();
            })
            ->with([
                'productBrand:id,name',
                'productImages:product_id,id,image'
            ])->withMin(
                'productDetails',
                'price'
            )->withMax(
                'productDetails',
                'price'
            );
    }
}
