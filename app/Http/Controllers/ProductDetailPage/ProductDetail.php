<?php

namespace App\Http\Controllers\ProductDetailPage;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductDetail
{
    public static function getProduct($product) {
        return $product
        ->load([
            'productBrand:id,name',
            'productImages:product_id,id,image',
            'productDetails'
        ])->loadMin(
            'productDetails',
            'price'
        )->loadMax(
            'productDetails',
            'price'
        );
    }
}
