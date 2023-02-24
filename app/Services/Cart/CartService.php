<?php
namespace App\Services\Cart;

use App\Models\Product;

interface CartService 
{
    public function getCart() : array;
    public function addQty(int $orderDetailId) : array;
    public function subQty(int $orderDetailId) : array;
    public function removeItem(int $orderDetailId) : array;
    public function addToCart(Product $product, int $productDetailId, int $qty) : array;
    public function checkoutCart() : array;
}