<?php

namespace App\Services\Payment;

interface PaymentService
{
    public function sendTransaction(string $bank): array;
}
