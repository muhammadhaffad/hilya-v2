<?php

namespace App\Http\Controllers\ProductDetailPage;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Str;

class Cart
{
    public static function addToCart($product, $productDetailId, $qty) {
        if (auth()->user()->checkout()->exists()) {
            return back()->withErrors([
                'add_to_cart' => 'Please complete your checkout first!'
            ]);
        }
        $productItem = $product->load([
            'productItems' => function ($query) use ($productDetailId) {
                $query->find($productDetailId);
            }])->productItems->first();
        if (!$productItem) {
            abort(404);
        }
        if ($productItem->stock < $qty) {
            return back()->withErrors([
                'qty' => 'Quantity must not exceed stock'
            ]);
        }
        $cart = auth()->user()->cart()->first();
        if (!$cart->exists())
            $cart = auth()->user()->cart()->create([
                'code' => Str::orderedUuid(),
                'status' => 'cart'
            ])->first();
        $isCreated = $cart->orderDetails()->create([
            'product_item_id' => $productItem->id,
            'qty' => $qty
        ]);
        if ($isCreated) {
            return back()->with([
                'success' => 'The product has been added to the cart'
            ]);
        } else {
            return back()->withErrors([
                'message' => 'The product fail added to the cart'
            ]);
        }
    }
}
