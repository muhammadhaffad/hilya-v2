<?php

namespace App\Http\Controllers\OrderHistoryPage;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderHistory
{
    public static function getOrders($keyword = null, $status = null, $startDate = null, $endDate = null)
    {
        $orders = auth()->user()->orders()->withoutStatus(['cart', 'checkout'])
            ->join('payments', 'payments.order_id', 'orders.id')
            ->join('order_details', 'order_details.order_id', 'orders.id')
            ->join('product_details', 'product_details.id', 'order_details.product_detail_id')
            ->join('products', 'products.id', 'product_details.product_id')
            ->join('product_brands', 'product_brands.id', 'products.product_brand_id')
            ->select('orders.*')
            ->distinct();
        if ($keyword) {
            $orders = $orders->where(function ($query) use ($keyword) {
                $query->where('orders.code', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('product_details.gender', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('product_details.age', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('product_details.model', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('product_brands.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('products.name', 'LIKE', '%' . $keyword . '%');
            });
        }
        if ($status) {
            $orders = $orders->where(function ($query) use ($status) {
                $query->withStatus([$status]);
            });
        }
        if ($startDate && $endDate) {
            $orders = $orders->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('payment.transactiontime', $startDate, $endDate);
            });
        }
        return $orders->with('payment:order_id,status,transactiontime');
    }
}
