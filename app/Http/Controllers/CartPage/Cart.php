<?php

namespace App\Http\Controllers\CartPage;

use App\Models\Order;
use App\Models\OrderItem;
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
        $orderItems = $order->orderItems()
            ->with([
                'productItem.product',
                'productItem.product.productImages',
                'productItem.product.productBrand'
            ])
            ->get();
        return compact('order', 'orderItems');
    }

    public static function addQty($order_item_id)
    {
        $orderItem = auth()->user()->cart()->first()->orderItems()->find($order_item_id);
        if (!$orderItem->exists())
            abort(404);
        $productItem = $orderItem->productItem()->first();
        if ($orderItem->qty < $productItem->stock) {
            $orderItem->qty += 1;
            $orderItem->save();
            return response('Quantity successfully added', 200);
        } else {
            return response('Quantity failed to add', 403);
        }
    }

    public static function subQty($order_item_id)
    {
        $orderItem = auth()->user()->cart()->first()->orderItems()->find($order_item_id);
        if (!$orderItem->exists())
            abort(404);
        if ($orderItem->qty > 0) {
            $orderItem->qty -= 1;
            $orderItem->save();
            return response('Quantity successfully added', 200);
        } else {
            return response('Quantity failed to add', 403);
        }
    }

    public static function removeItem($order_item_id)
    {
        $orderItem = auth()->user()->cart()->first()->orderItems()->find($order_item_id);
        if (!$orderItem->exists())
            abort(404);
        if ($orderItem->delete()) {
            return back()->with('success', 'Item successfully removed');
        } else {
            abort(500);
        }
    }

    private static function productsUnavailable ($order)
    {
        return $order->join('order_items', 'order_id', 'orders.id')
        ->join('product_items', function ($join) {
            $join->on('product_items.id', '=', 'order_items.product_item_id')
                ->on('product_items.stock', '<', 'order_items.qty');
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
