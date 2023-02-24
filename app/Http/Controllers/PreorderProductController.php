<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PreorderProductPage\PreorderProduct;
use App\Http\Controllers\PreorderProductPage\ProductSearch;
use App\Models\ProductBrand;
use Illuminate\Http\Request;

class PreorderProductController extends Controller
{
    public function index(Request $request) {
        if ($request->get('q')) 
            $products = ProductSearch::search($request->get('q'))->get();
        else 
            $products = PreorderProduct::getPreorderProducts()->paginate(12);
        $productBrands = ProductBrand::all();
        return response()->json(compact('products', 'productBrands'));
    }
}
