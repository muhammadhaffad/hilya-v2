<?php

namespace App\Http\Controllers\CheckoutPage;

class Address
{
    public static function createAddress($request)
    {
        return auth()->user()->shippingAddresses()->create([
            'addressname' => $request->addressname,
            'province' => $request->province,
            'regency' => $request->regency,
            'district' => $request->district,
            'zip' => $request->zip,
            'shippingname' => $request->shippingname,
            'phonenumber' => $request->phonenumber,
            'fulladdress' => $request->fulladdress,
            'isselect' => 0,
            'type' => 'secondary'
        ]);
    }

    public static function updateAddress($request, $id)
    {
        $address = auth()->user()->shippingAddresses()->find($id);
        if (!$address->exists()) {
            abort(404);
        }
        return $address->update([
                'addressname' => $request->addressname,
                'province' => $request->province,
                'regency' => $request->regency,
                'district' => $request->district,
                'zip' => $request->zip,
                'shippingname' => $request->shippingname,
                'phonenumber' => $request->phonenumber,
                'fulladdress' => $request->fulladdress
            ]);
    }

    public static function deleteAddress($id)
    {
        $address = auth()->user()->shippingAddresses()->find($id);
        if (!$address->exists()) {
            abort(404);
        }
        return $address->delete();
    }

    public static function selectAddress($id) 
    {
        auth()->user()->addressSelected()->update(['isselect' => 0]);
        return auth()->user()->shippingAddresses()->find($id)->update(['isselect' => 1]);       
    }
}
