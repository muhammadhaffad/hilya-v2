<?php

namespace App\Http\Controllers\OrderHistoryPage;

use App\Models\Order;
use App\Models\OrderDetail as ModelsOrderDetail;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderDetail
{
    public static function getOrder($code)
    {
        $order = auth()->user()->orders()->code($code)
                       ->with('payment:order_id,transactiontime,status')
                       ->first();
        if (!$order) {
            return abort(404);
        }
        $orderDetails = $order->orderDetails()
            ->with(['productDetail.product', 
                'productDetail.product.productImages', 
                'productDetail.product.productBrand'
            ])
            ->get();
        $shipping = $order->shipping()->with('shippingAddress')->first();
        $payment = $order->payment()->first();
        return compact('order', 'orderDetails', 'shipping', 'payment');
    }
}
