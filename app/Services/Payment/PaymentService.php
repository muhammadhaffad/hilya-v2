<?php

namespace App\Services\Payment;

interface PaymentService
{
    public function sendTransaction($checkoutInformation, string $bank): array;
}
