<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function signUp(SignUpRequest $request) {
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
        return redirect()->route('login')->with(['success' => 'Succesfull registration, you can sign in']);
    }
}
