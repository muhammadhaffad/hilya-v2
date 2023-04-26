<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CartPage\Cart;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function test()
    {
        $name = 'abcd';
        $array = [0=>1,2=>1,3=>1,5=>3,6=>1,7=>1];
        $validator = Validator::make(compact('name', 'array'), [
            'name' => 'required|numeric',
            'array.*' => 'required|numeric|min:2'
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }

    public function index()
    {
        $hasCheckout = $this->cartService->hasCheckout();
        if ($hasCheckout) {
            return redirect()->route('customer.checkout')->with('message', $hasCheckout['message']);
        }
        $cart = $this->cartService->getCart();
        if ($cart['code'] == 200) {
            return view('v2.customer.cart.index', ['cartItems' => $cart['data']]);
        } else {
            return view('v2.customer.cart.index', ['cartItems' => (object)['orderItems'=>[]]]);
        }
    }

    public function add(Request $request)
    {
        $hasCheckout = $this->cartService->hasCheckout();
        if ($hasCheckout) {
            return redirect()->route('customer.checkout')->with('message', $hasCheckout['message']);
        }
        $result = $this->cartService->addQty((int)$request->order_item_id);
        if ($result['code'] == 204) {
            return redirect()->back();
        } else if ($result['code'] == 422) {
            return redirect()->back()->withErrors($result['errors'], 'qtyErrors');
        } else {
            return abort(404);
        }
    }

    public function sub(Request $request)
    {
        $hasCheckout = $this->cartService->hasCheckout();
        if ($hasCheckout) {
            return redirect()->route('customer.checkout')->with('message', $hasCheckout['message']);
        }
        $result = $this->cartService->subQty((int)$request->order_item_id);
        if ($result['code'] == 204) {
            return redirect()->back();
        } else if ($result['code'] == 422) {
            return redirect()->back()->withErrors($result['errors'], 'qtyErrors');
        } else {
            return abort(404);
        }
    }

    public function remove(Request $request)
    {
        $hasCheckout = $this->cartService->hasCheckout();
        if ($hasCheckout) {
            return redirect()->route('customer.checkout')->with('message', $hasCheckout['message']);
        }
        $result = $this->cartService->removeItem((int)$request->order_item_id);
        if ($result['code'] == 204) {
            return redirect()->back()->with('message', $result['message']);
        } else {
            return abort(404);
        }
    }

    public function checkout(Request $request)
    {
        $hasCheckout = $this->cartService->hasCheckout();
        if ($hasCheckout) {
            return redirect()->route('customer.checkout')->with('message', $hasCheckout['message']);
        }
        $hasUnavailableProducts = $this->cartService->checkUnvailableProducts();
        if ($hasUnavailableProducts) {
            return redirect()->route('customer.cart')->withErrors($hasUnavailableProducts['errors'], 'cartItemErrors');
        }
        $result = $this->cartService->checkoutCart();
        if ($result['code'] == 204) {
            return redirect()->route('customer.checkout')->with('message', $result['message']);
        }
        if ($result['code'] == 302) {
            return redirect()->route('customer.address-book')->with('message', $result['message']);
        }
        return abort(500);
    }
}
