<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ReadyProductPage\ProductSearch;
use App\Http\Controllers\ReadyProductPage\ReadyProduct;
use App\Models\ProductBrand;
use Illuminate\Http\Request;

class ReadyProductController extends Controller
{
    public function index(Request $request) {
        if ($request->get('q')) 
            $products = ProductSearch::search($request->get('q'))->get();
        else 
            $products = ReadyProduct::getReadyProducts()->paginate(12);
        $productBrands = ProductBrand::all();
        return response()->json(compact('products', 'productBrands'));
    }
}
