<?php

namespace App\Http\Controllers\ShippingDetail;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;

class ShippingDetail
{
    public static function getAddresses() {
        return auth()->user()->shippingAddresses();
    }

    public static function selectAddress($id) {
        if (!auth()->user()->shippingAddresses()->find($id)->exists() || !auth()->user()->checkout()->exists())
            abort(404);
        return auth()->user()->checkout()->first()->shipping()
                     ->update(['shipping_address_id'=>$id]);
    }

    public static function getShipping() {
        return auth()->user()->checkout()->first()
                     ->shipping()->with('shippingAddress')->get();
    }

    public static function postageCheck() {
        /** TODO */
    }
}
