<?php

namespace App\Http\Controllers\CheckoutPage;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Payment
{
    private static function checkProductUnavailable($order)
    {
        return $order->join('order_items', 'order_id', 'orders.id')
            ->join('product_items', function ($join) {
                $join->on('product_items.id', '=', 'order_items.product_item_id')
                    ->on('product_items.stock', '<', 'order_items.qty');
            })->get();
    }
    private static function calcSubTotalOrderItems($order)
    {
        return $order->join('order_items', 'order_id', 'orders.id')
            ->join('product_items', function ($join) {
                $join->on('product_items.id', '=', 'order_items.product_item_id');
            })->select(DB::raw('sum(order_items.qty * product_items.price) as subtotal'))->first()->subtotal;
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
