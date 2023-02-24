<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CartPage\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return response()->json(Cart::getCart());
    }

    public function add(Request $request, $order_detail_id)
    {
        return response()->json(Cart::addQty($order_detail_id));
    }
    
    public function sub(Request $request, $order_detail_id)
    {
        return response()->json(Cart::subQty($order_detail_id));
    }

    public function remove(Request $request, $order_detail_id)
    {
        return response()->json(Cart::removeItem($order_detail_id));
    }
}
