<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart\CartService;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $cartService;
    protected $productService;

    public function __construct(ProductService $productService, CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
    }

    public function show(Product $product) {
        $result = $this->productService->getProduct($product);
        if ($result['code'] == 200) {
            return view('product.show', ['product' => $result['data']]);
        } else {
            abort(404);
        }
    }
    public function addToCart(Request $request, Product $product) 
    {
        $hasCheckout = $this->cartService->hasCheckout();
        if ($hasCheckout) {
            return redirect()->route('customer.checkout')->with('message', $hasCheckout['message']);
        }
        $result = $this->cartService->addToCart($product, $request->all());
        if ($result['code'] == 204) {
            return redirect()->back()->with('message', $result['message']);
        } else if ($result['code'] == 422) {
            return redirect()->back()->withErrors($result['errors'], 'addToCartErrors');
        } else {
            return abort(500);
        }
    }
}
