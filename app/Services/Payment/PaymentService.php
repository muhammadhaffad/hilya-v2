<?php

namespace App\Services\Payment;

interface PaymentService
{
    public function sendTransaction($checkoutInformation, int $shippingCost, int $grossAmount, string $bank): array;
}
