<?php
namespace App\Services\Order;

interface OrderService
{
    public function getCountOrder() : array;
    public function getOrders(int $userId) : array;
    public function getDetailOrder(int $userId, string $code) : array;
    public function searchOrders(int $userId, $criteria) : array;
    public function setDelivered(int $userId, string $code) : array;
    public function setProcessing(string $code) : array;
    public function setShipping(string $code, array $attr) : array;
    public function setSuccess(string $code) : array;
}