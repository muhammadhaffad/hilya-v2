<?php

namespace App\Http\Controllers\BrandProductPage;

use App\Models\Product;
use Illuminate\Http\Request;

class BrandProduct
{
    public static function getBrandProducts($brand) {
        return Product::withBrand([$brand->id])
            ->whereHas('productItems', fn ($q) => $q->inStock())
            ->with([
                'productBrand:id,name',
                'productImages:product_id,id,image'
            ])->withMin(
                'productItems', 'price'
            )->withMax(
                'productItems', 'price'
            );
    }
}
