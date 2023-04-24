<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart\CartService;
use App\Services\Product\ProductService;
use App\Services\ProductBrand\ProductBrandService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $cartService;
    protected $productService;
    protected $productBrandService;

    public function __construct(ProductService $productService, ProductBrandService $productBrandService, CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
        $this->productBrandService = $productBrandService;
    }

    public function show(Product $product) {
        $result = $this->productService->getProduct($product);
        if ($result['code'] == 200) {
            return view('v2.public.product.show', ['product' => $result['data']]);
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

    public function searchProducts(Request $request)
    {
        $result = $this->productService->searchProducts($request->all(), limit:20);
        if ($result['code'] == 200) {
            return response()->json($result['data'], 200);
        } else {
            return response()->json($result['message'], 404);
        }
    }

    public function ready(Request $request)
    {
        $result = $this->productBrandService->getAllBrand();
        if ($result['code'] == 200) {
            $productBrands = $result['data'];
        } else {
            $productBrands = [];
        }
        $result = $this->productService->getProductsByAvailability('ready', paginate:12);
        if ($result['code'] == 200) {
            $readyProducts = $result['data'];
        } else {
            $readyProducts = [];
        }
        return view('v2.public.product.ready', compact('readyProducts', 'productBrands'));
    }

    public function preorder(Request $request)
    {
        $result = $this->productBrandService->getAllBrand();
        if ($result['code'] == 200) {
            $productBrands = $result['data'];
        } else {
            $productBrands = [];
        }
        $result = $this->productService->getProductsByAvailability('preorder', paginate:12);
        if ($result['code'] == 200) {
            $preorderProducts = $result['data'];
        } else {
            $preorderProducts = [];
        }
        return view('v2.public.product.preorder', compact('preorderProducts', 'productBrands'));
    }

    public function promo(Request $request)
    {
        $result = $this->productBrandService->getAllBrand();
        if ($result['code'] == 200) {
            $productBrands = $result['data'];
        } else {
            $productBrands = [];
        }
        $result = $this->productService->getProductsPromo(paginate:12);
        if ($result['code'] == 200) {
            $promoProducts = $result['data'];
        } else {
            $promoProducts = [];
        }
        return view('v2.public.product.promo', compact('promoProducts', 'productBrands'));
    }
}
