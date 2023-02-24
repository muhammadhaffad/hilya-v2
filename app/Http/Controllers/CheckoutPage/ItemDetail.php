<?php

namespace App\Http\Controllers\CheckoutPage;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;

class ItemDetail
{
    public static function getItems() 
    {
        $order = auth()->user()->cart()->first();
        $orderDetails = $order->orderDetails()
            ->with(['product_detail.product', 
                'productDetail.product.product_images', 
                'productDetail.product.product_brand'
            ])
            ->get();
        return compact('order', 'orderDetails');
    }
}
