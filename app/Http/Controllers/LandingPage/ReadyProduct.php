<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Product;
use Illuminate\Http\Request;

class ReadyProduct
{
    public static function getReadyProducts()
    {
        return Product::ready()->whereHas('productItems', function ($query) {
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
