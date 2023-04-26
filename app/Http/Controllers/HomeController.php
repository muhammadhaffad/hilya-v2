<?php

namespace App\Http\Controllers;

use App\Http\Controllers\LandingPage\PreorderProduct;
use App\Http\Controllers\LandingPage\PromoProduct;
use App\Http\Controllers\LandingPage\ReadyProduct;
use App\Http\Controllers\LandingPage\Slide;
use App\Models\ProductBrand;
use App\Services\Product\ProductService;
use App\Services\ProductBrand\ProductBrandService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $productService;
    protected $productBrandService;
    public function __construct(ProductService $productService, ProductBrandService $productBrandService)
    {
        $this->productService = $productService;
        $this->productBrandService = $productBrandService;
    }
    public function index(Request $request)
    {
        $result = $this->productBrandService->getAllBrand();
        if ($result['code'] == 200) {
            $productBrands = $result['data'];
        } else {
            $productBrands = [];
        }
        $result = $this->productService->getProductsByAvailability('ready', 0, $request->get('limit') ?? 10);
        if ($result['code'] == 200) {
            $readyProducts = $result['data'];
        } else {
            $readyProducts = [];
        }
        $result = $this->productService->getProductsPromo(0, 10);
        if ($result['code'] == 200) {
            $promoProducts = $result['data'];
        } else {
            $promoProducts = [];
        }
        $result = $this->productService->getProductsByAvailability('preorder', 0, 10);
        if ($result['code'] == 200) {
            $preorderProducts = $result['data'];
        } else {
            $preorderProducts = [];
        }
        return view('v2.public.index', compact('productBrands', 'readyProducts', 'promoProducts', 'preorderProducts'));
    }
}
