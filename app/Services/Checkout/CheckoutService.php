<?php

namespace App\Services\Checkout;

interface CheckoutService
{
    public function changeAddressShipping(int $shippingAddressId): array;
    public function getAllShippingCost(): array;
    public function placeOrder(string $courier, string $service, string $bank): array;
    public function backToCart(): array;
}
