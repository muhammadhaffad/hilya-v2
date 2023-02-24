<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PromoProductPage\ProductSearch;
use App\Http\Controllers\PromoProductPage\PromoProduct;
use App\Models\ProductBrand;
use Illuminate\Http\Request;

class PromoProductController extends Controller
{
    public function index(Request $request) {
        if ($request->get('q')) 
            $products = ProductSearch::search($request->get('q'))->get();
        else 
            $products = PromoProduct::getPreorderProducts()->paginate(12);
        $productBrands = ProductBrand::all();
        return response()->json(compact('products', 'productBrands'));
    }
}
