<?php

namespace App\Http\Controllers\ReadyProductPage;

use App\Models\Product;
use Illuminate\Http\Request;

class ReadyProduct
{
    public static function getReadyProducts() {
        return Product::ready()->whereHas('productDetails', fn ($q) => $q->inStock())
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
