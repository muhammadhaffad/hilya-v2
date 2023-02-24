<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProductDetailPage\Cart;
use App\Http\Controllers\ProductDetailPage\MoreProduct;
use App\Http\Controllers\ProductDetailPage\ProductItem;
use App\Http\Requests\AddToCartRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function index(Product $product) 
    {
        $detailProduct = ProductItem::getProduct($product);
        $moreProducts = MoreProduct::getMoreProducts()->limit(12)->get();
        return response()->json(compact('detailProduct', 'moreProducts'));
    }

    public function addToCart(Product $product, AddToCartRequest $request) 
    {
        return Cart::addToCart($product, $request->product_item_id, $request->qty);
    }
}
