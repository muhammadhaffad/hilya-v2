<?php
namespace App\Services\Cart;

use App\Models\Product;

interface CartService 
{
    public function hasCheckout(): mixed;
    public function checkUnvailableProducts(): mixed;
    public function getCart() : mixed;
    public function addQty(int $orderDetailId) : mixed;
    public function subQty(int $orderDetailId) : mixed;
    public function removeItem(int $orderDetailId) : mixed;
    public function addToCart(Product $product, array $attr) : mixed;
    public function checkoutCart() : mixed;
}