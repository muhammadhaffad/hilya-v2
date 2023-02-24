<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductDetailPage\Cart;
use App\Http\Controllers\ProductDetailPage\MoreProduct;
use App\Http\Controllers\ProductDetailPage\ProductDetail;
use App\Http\Requests\AddToCartRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function index(Product $product) 
    {
        $detailProduct = ProductDetail::getProduct($product);
        $moreProducts = MoreProduct::getMoreProducts()->limit(12)->get();
        return response()->json(compact('detailProduct', 'moreProducts'));
    }

    public function addToCart(Product $product, AddToCartRequest $request) 
    {
        return Cart::addToCart($product, $request->product_detail_id, $request->qty);
    }
}
