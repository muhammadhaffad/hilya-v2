<?php

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use App\Services\Product\ProductService;
use App\Services\ProductBrand\ProductBrandService;
use Illuminate\Http\Request;

class BrandProductController extends Controller
{
    protected $productService;
    protected $productBrandService;
    public function __construct(ProductService $productService, ProductBrandService $productBrandService)
    {
        $this->productService = $productService;
        $this->productBrandService = $productBrandService;
    }
    public function index(ProductBrand $brand, Request $request) {
        if ($request->get('q') !== null) 
        {
            $products = $this->productService->searchProductsByBrand($request, array($brand->id), paginate:16);
            $products['data'] ?: abort(404);
        }
        else 
        {
            $products = $this->productService->getProductsByBrand(array($brand->id), paginate:16);
            $products['data'] ?: abort(404);
        }
        $productBrands = $this->productBrandService->getAllBrand();
        return response()->json(compact('products', 'productBrands'));
    }
}
