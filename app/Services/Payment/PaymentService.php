<?php

namespace App\Services\Payment;

interface PaymentService
{
    public function sendTransaction($transactionDetails, $itemDetails, string $bank): array;
}
