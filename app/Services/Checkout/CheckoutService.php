<?php

namespace App\Services\Checkout;

interface CheckoutService
{
    public function hasNoCheckout(): mixed;
    public function checkUnvailableProducts(): mixed;
    public function getCheckoutItems(): array;
    public function getAddresses(): array;
    public function changeAddressShipping(array $attr): array;
    public function getAllShippingCost(): array;
    public function placeOrder(array $attr): array;
    public function backToCart(): array;
}
