<?php
namespace App\Services\Order;

interface OrderService
{
    public function getOrders(int $userId) : array;
    public function getDetailOrder(int $userId, string $code) : array;
    public function searchOrders(int $userId, $criteria) : array;
}