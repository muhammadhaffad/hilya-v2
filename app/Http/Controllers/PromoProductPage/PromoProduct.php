<?php

namespace App\Http\Controllers\PromoProductPage;

use App\Models\Product;
use Illuminate\Http\Request;

class PromoProduct
{
    public static function getPreorderProducts() {
        return Product::promo()->whereHas('productDetails', fn ($q) => $q->inStock())
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
