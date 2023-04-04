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
    public function index()
    {
        $slides = $this->productService->getProductsByAvailability('ready', 0, 2);
        $productBrands = $this->productBrandService->getAllBrand();
        $readyProducts = $this->productService->getProductsByAvailability('ready', 0, 4);
        $promoProducts = $this->productService->getProductsPromo(0, 4);
        $preorderProducts = $this->productService->getProductsByAvailability('preorder', 0, 4);
        return response()->json(compact('slides', 'productBrands', 'readyProducts', 'promoProducts', 'preorderProducts'));
    }
}
