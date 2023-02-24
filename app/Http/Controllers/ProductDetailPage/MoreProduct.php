<?php

namespace App\Http\Controllers\ProductDetailPage;

use App\Models\Product;
use Illuminate\Http\Request;

class MoreProduct
{
    public static function getMoreProducts() {
        return Product::whereHas('productItems', fn ($q) => $q->inStock())
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
