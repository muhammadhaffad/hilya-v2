<?php

namespace App\Http\Controllers\CheckoutPage;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Payment
{
    private static function checkProductUnavailable($order)
    {
        return $order->join('order_details', 'order_id', 'orders.id')
            ->join('product_details', function ($join) {
                $join->on('product_details.id', '=', 'order_details.product_detail_id')
                    ->on('product_details.stock', '<', 'order_details.qty');
            })->get();
    }
    private static function calcSubTotalOrderItems($order)
    {
        return $order->join('order_details', 'order_id', 'orders.id')
            ->join('product_details', function ($join) {
                $join->on('product_details.id', '=', 'order_details.product_detail_id');
            })->select(DB::raw('sum(order_details.qty * product_details.price) as subtotal'))->first()->subtotal;
    }
    public static function processPayment($service, $bank)
    {
        $checkout = auth()->user()->checkout();
        if (!$checkout->exists())
            abort(500);
        $productsUnavailable = Payment::checkProductUnavailable($checkout)->pluck('id');
        if ($productsUnavailable) {
            return back()->withErrors(['message' => 'Products unavailable', 'product_ids' => $productsUnavailable]);
        }
        /** TODO: Get shipping cost */
        $checkout->first()->shipping()->update([
            'service' => $service,
            'shippingcost' => 10000
        ]);
        $checkout->subtotal = Payment::calcSubTotalOrderItems($checkout);
        $checkout->grandtotal = $checkout->subtotal + $checkout->first()->shipping()->first()->shippingcost;
        $checkout->save();
        /** TODO: Request payment */
        return $checkout->payment()->create([
            'bank' => $bank,
            'vanumber' => '123',
            'amount' => '123123',
            'status' => 'pending',
            'transactiontime' => 'Y-m-d H:i:s'
        ]);
    }
    public static function cancelOrder()
    {
        $order = auth()->user()->checkout()->first();
        $order->status = 'cart';
        return $order->save();
    }
}
