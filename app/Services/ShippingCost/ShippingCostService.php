<?php
namespace App\Services\ShippingCost;

interface ShippingCostService
{
    public function getCosts(int $origin, int $destination, int $weight, string $courier): array;
}