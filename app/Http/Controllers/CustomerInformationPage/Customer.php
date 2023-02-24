<?php

namespace App\Http\Controllers\CustomerInformationPage;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Customer
{
    public static function getCustomerInformation() 
    {
        return auth()->user()->load('shippingAddress');
    }
}
