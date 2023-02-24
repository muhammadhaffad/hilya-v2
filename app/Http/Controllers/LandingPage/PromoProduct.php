<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Product;
use Illuminate\Http\Request;

class PromoProduct
{
    public static function getPromoProducts()
    {
        return Product::promo()->whereHas('productItems', function ($query) {
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
