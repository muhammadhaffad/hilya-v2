<?php

namespace App\Http\Controllers\RegisterPage;

use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterForm
{
    public static function signUp($request) {
        $user = User::create([
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'fullname' => $request->fullname,
            'phonenumber' => $request->phonenumber
        ]);
        ShippingAddress::create([
            'user_id' => $user->id,
            'addressname' => 'rumah',
            'shippingname' => $user->fullname,
            'phonenumber' => $user->phonenumber,
            'province' => $request->province,
            'regency' => $request->regency,
            'district' => $request->district,
            'zip' => $request->zip,
            'fulladdress' => $request->fulladdress,
            'isselect' => true,
            'type' => 'primary'
        ]);
        return true;
    }
}
