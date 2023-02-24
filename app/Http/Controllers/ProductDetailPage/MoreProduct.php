<?php

namespace App\Http\Controllers\ProductDetailPage;

use App\Models\Product;
use Illuminate\Http\Request;

class MoreProduct
{
    public static function getMoreProducts() {
        return Product::whereHas('productDetails', fn ($q) => $q->inStock())
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
