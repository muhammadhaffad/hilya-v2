<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductOrigin;
use App\Services\Checkout\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $checkoutService;
    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function index()
    {
        $result = $this->checkoutService->hasNoCheckout();
        if ($result) {
            return redirect()->route('customer.cart')->with('message', $result['message']);
        }
        $result = $this->checkoutService->getCheckoutItems();
        if ($result['code'] == 200) {
            $checkoutItems = $result['data'];
        } else {
            abort(404);
        }
        $result = $this->checkoutService->getAddresses();
        if ($result['code'] == 200) {
            $addresses = $result['data'];
        } else {
            abort(404);
        }
        $result = $this->checkoutService->getAllShippingCost();
        if ($result['code'] == 200) {
            $shippingCost = $result['data'];
        } else {
            abort(404);
        }
        return view('v2.customer.checkout.index', compact('checkoutItems', 'addresses', 'shippingCost'));
    }
    public function changeAddressShipping(Request $request)
    {
        $result = $this->checkoutService->changeAddressShipping($request->all());
        if ($result['code'] == 204) {
            return redirect()->back()->with('message', $result['message']);
        } else if ($result['code'] == 422) {
            return redirect()->back()->withErrors($result['errors']);
        } else {
            return abort(500);
        }
    }
    public function placeOrder(Request $request)
    {
        $result = $this->checkoutService->hasNoCheckout();
        if ($result) {
            return redirect()->route('customer.cart')->with('message', $result['message']);
        }
        $hasUnavailableProducts = $this->checkoutService->checkUnvailableProducts();
        if ($hasUnavailableProducts) {
            return redirect()->back()->withErrors($hasUnavailableProducts['errors'], 'checkoutItemErrors');
        }
        $result = $this->checkoutService->placeOrder($request->all());
        if ($result['code'] == 201) {
            return redirect()->route('customer.orders.show', $result['data'])->with('message', $result['message']);
        } else if ($result['code'] == 422) {
            return redirect()->back()->withErrors($result['errors'], $result['bag']);
        } else if ($result['code'] == 500) {
            return abort(500, $result['message']);
        } else {
            return abort(500);
        }
    }
    public function backToCart(Request $request)
    {
        $result = $this->checkoutService->backToCart();
        if ($result['code'] == 204) {
            return redirect()->route('customer.cart')->with('message', $result['message']);
        } else {
            return abort(500);
        }
    }
}
