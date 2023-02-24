<?php

namespace App\Http\Controllers\CartPage;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Cart
{
    public static function isCheckout()
    {
        return auth()->user()->checkout()->exists();
    }
    public static function getCart()
    {
        $order = auth()->user()->cart()->first();
        if (!$order->exists())
            abort(404);
        $orderDetails = $order->orderDetails()
            ->with([
                'productItem.product',
                'productItem.product.productImages',
                'productItem.product.productBrand'
            ])
            ->get();
        return compact('order', 'orderDetails');
    }

    public static function addQty($order_detail_id)
    {
        $orderDetail = auth()->user()->cart()->first()->orderDetails()->find($order_detail_id);
        if (!$orderDetail->exists())
            abort(404);
        $productItem = $orderDetail->productItem()->first();
        if ($orderDetail->qty < $productItem->stock) {
            $orderDetail->qty += 1;
            $orderDetail->save();
            return response('Quantity successfully added', 200);
        } else {
            return response('Quantity failed to add', 403);
        }
    }

    public static function subQty($order_detail_id)
    {
        $orderDetail = auth()->user()->cart()->first()->orderDetails()->find($order_detail_id);
        if (!$orderDetail->exists())
            abort(404);
        if ($orderDetail->qty > 0) {
            $orderDetail->qty -= 1;
            $orderDetail->save();
            return response('Quantity successfully added', 200);
        } else {
            return response('Quantity failed to add', 403);
        }
    }

    public static function removeItem($order_detail_id)
    {
        $orderDetail = auth()->user()->cart()->first()->orderDetails()->find($order_detail_id);
        if (!$orderDetail->exists())
            abort(404);
        if ($orderDetail->delete()) {
            return back()->with('success', 'Item successfully removed');
        } else {
            abort(500);
        }
    }

    private static function productsUnavailable ($order)
    {
        return $order->join('order_details', 'order_id', 'orders.id')
        ->join('product_items', function ($join) {
            $join->on('product_items.id', '=', 'order_details.product_item_id')
                ->on('product_items.stock', '<', 'order_details.qty');
        })->get()->mapWithKeys(fn ($item, $key) => ["product_items.$item->product_item_id" => "Produk sudah habis"]);
    }

    public static function processCheckout()
    {
        $cart = auth()->user()->cart()->first();
        $productsUnavailable = Cart::productsUnavailable($cart)->pluck('id');
        if ($productsUnavailable) {
            return back()->withErrors(['message' => 'Products unavailable', 'product_ids' => $productsUnavailable]);
        }
        $cart->shipping()->create(
            ['shipping_address_id', function () {
                $addresses = auth()->user()->shippingAddresses();
                if ($addresses->selected()->exists())
                    return $addresses->selected()->first()->id;
                return $addresses->primary()->first()->id;
            }]
        );
        $cart->status = 'checkout';
        return $cart->save();
    }
}
