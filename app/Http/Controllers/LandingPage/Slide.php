<?php

namespace App\Http\Controllers\LandingPage;

use App\Models\Product;
use Illuminate\Http\Request;

class Slide
{
    public static function getSlides() {
        return Product::whereHas('productItems', function ($query) {
                $query->inStock();
            })->limit(6)
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
