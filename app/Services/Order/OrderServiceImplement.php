<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Payment;

class OrderServiceImplement implements OrderService
{
    private function calcDiscount($code): int
    {
        $order = Payment::where('order_code', $code)->first()->order()->first()->load('orderItems.productItem');
        $total = 0;
        $customProps = collect($order->custom_properties);
        foreach ($order->orderItems as $orderItem) {
            $prop = $customProps->where('id', $orderItem->productItem->id)->first();
            if ($prop) {
                $total += $orderItem->qty * (int)($prop['price']*$prop['discount']/100);
            }
        }
        return $total;
    }

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
                'orderItems.productItem.product:id,product_brand_id,name',
                'orderItems.productItem.product.productImage',
                'orderItems.productItem.product.productBrand',
                'payment:id,order_id,transactiontime,settlementtime,amount'
            ])->orderBy('created_at', 'desc')->get();
        if($orders) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan data order',
                'data' => $orders
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        };
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
        $order = Payment::where('order_code', $code)->first()->order()->where('user_id', $userId)->with([
            'user:id,fullname',
            'orderItems.productItem.product.productImage',
            'orderItems.productItem.product.productBrand',
            'shipping.shippingAddress',
            'payment'
        ])->first();
        $order['total_discount'] = $this->calcDiscount($code);
        if ($order) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan detail order',
                'data' => $order
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
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
            ->join('order_items', 'order_items.order_id', 'orders.id')
            ->join('product_items', 'product_items.id', 'order_items.product_item_id')
            ->join('products', 'products.id', 'product_items.product_id')
            ->join('product_brands', 'product_brands.id', 'products.product_brand_id')
            ->select('orders.*')
            ->distinct();
        if (@$criteria['search']) {
            $orders->where(function ($query) use ($criteria) {
                $query->where('payments.order_code', 'LIKE', '%' . $criteria['search'] . '%')
                    ->orWhere('product_items.gender', 'LIKE', '%' . $criteria['search'] . '%')
                    ->orWhere('product_items.age', 'LIKE', '%' . $criteria['search'] . '%')
                    ->orWhere('product_brands.name', 'LIKE', '%' . $criteria['search'] . '%')
                    ->orWhere('products.name', 'LIKE', '%' . $criteria['search'] . '%');
            });
        }
        if (@$criteria['status']) {            
            $orders->where(function ($query) use ($criteria) {
                $query->withStatus([$criteria['status']]);
            });
        }
        if (@$criteria['start_date'] !== null && @$criteria['end_date'] !== null) {
            $orders->where(function ($query) use ($criteria) {
                $query->whereBetween('orders.created_at', [$criteria['start_date'], $criteria['end_date']]);
            });
        } else {
            $orders->where(fn ($q) => $q->whereDate('orders.created_at', '>=', now()->subDays(30)));
        }
        $orders = $orders->with([
            'productItems.product:id,product_brand_id,name',
            'productItems.product.productImage',
            'productItems.product.productBrand',
            'payment'
        ])->orderBy('created_at', 'desc')->get();
        if ($orders) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan data order',
                'data' => $orders
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
    }

    public function getCountOrder() : array
    { 
        $cartTotal = Order::where([['status','cart'], ['user_id', auth()->user()->id]])
            ->withCount('orderItems')
            ->first()
            ?->order_items_count ?? 0;
        $unpaidTotal = Order::where([['status','pending'], ['user_id', auth()->user()->id]])->count();
        $paidTotal = Order::where([['status','paid'], ['user_id', auth()->user()->id]])->count();
        $readyTotal = Order::where([['status','ready'], ['user_id', auth()->user()->id]])->count();
        $shippingTotal = Order::where([['status','shipping'], ['user_id', auth()->user()->id]])->count();
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data jumlah order',
            'data' => [
                'Keranjang' => $cartTotal,
                'Belum Bayar' => $unpaidTotal,
                'Menunggu Konfirmasi' => $paidTotal,
                'Sudah Diproses' => $readyTotal,
                'Dalam Pengiriman' => $shippingTotal
            ]
        ];         
    }
}
