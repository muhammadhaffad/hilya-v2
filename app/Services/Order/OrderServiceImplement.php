<?php

namespace App\Services\Order;

use App\Models\Order;

class OrderServiceImplement implements OrderService
{
    /**
     * getOrders
     *
     * @param  int $userId
     * @return array
     */
    public function getOrders(int $userId): array
    {
        $orders = Order::where('user_id', $userId)->withoutStatus(['cart', 'checkout'])
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->with([
                'productItems.product:id,product_brand_id,name',
                'productItems.product.productImage',
                'productItems.product.productBrand',
                'payment:id,order_id,transactiontime,settlementtime,amount'
            ])->orderBy('created_at', 'desc')->get();
        return array(
            'code' => $orders->all() ? 200 : 404,
            'data' => $orders->all()
        );
    }

    /**
     * getDetailOrder
     *
     * @param  int $userId
     * @param  string $code
     * @return array
     */
    public function getDetailOrder(int $userId, string $code): array
    {
        $order = Order::where('user_id', $userId)->code($code)->with([
            'user:id,fullname',
            'orderDetails.productItem.product.productImage',
            'orderDetails.productItem.product.productBrand',
            'shipping.shippingAddress',
            'payment'
        ])->first();
        return array(
            'code' => $order ? 200 : 404,
            'data' => $order
        );
    }

    /**
     * searchOrders
     *
     * @param  int $userId
     * @param  mixed $criteria
     * @return array
     */
    public function searchOrders(int $userId, $criteria): array
    {
        $orders = Order::where('user_id', $userId)->withoutStatus(['cart', 'checkout'])
            ->join('payments', 'payments.order_id', 'orders.id')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('product_items', 'product_items.id', 'order_details.product_item_id')
            ->join('products', 'products.id', 'product_items.product_id')
            ->join('product_brands', 'product_brands.id', 'products.product_brand_id')
            ->select('orders.*')
            ->distinct();
        if ($criteria['search']) {
            $orders->where(function ($query) use ($criteria) {
                $query->where('orders.code', 'LIKE', '%' . $criteria['search'] . '%')
                    ->orWhere('product_items.gender', 'LIKE', '%' . $criteria['search'] . '%')
                    ->orWhere('product_items.age', 'LIKE', '%' . $criteria['search'] . '%')
                    ->orWhere('product_items.model', 'LIKE', '%' . $criteria['search'] . '%')
                    ->orWhere('product_brands.name', 'LIKE', '%' . $criteria['search'] . '%')
                    ->orWhere('products.name', 'LIKE', '%' . $criteria['search'] . '%');
            });
        }
        if ($criteria['status']) {
            $orders->where(function ($query) use ($criteria) {
                $query->withStatus([$criteria['status']]);
            });
        }
        if ($criteria['start_date'] && $criteria['end_date']) {
            $orders->where(function ($query) use ($criteria) {
                $query->whereBetween('orders.created_at', [$criteria['start_date'], $criteria['end_date']]);
            });
        } else {
            $orders->where(fn ($q) => $q->whereDate('orders.created_at', '>=', now()->subDays(30)));
        }
        $orders->with([
            'productItems.product:id,product_brand_id,name',
            'productItems.product.productImage',
            'productItems.product.productBrand',
            'payment:id,order_id,transactiontime,settlementtime,amount'
        ])->orderBy('created_at', 'desc')->get();
        return array(
            'code' => $orders->all() ? 200 : 404,
            'data' => $orders->all()
        );
    }
}
