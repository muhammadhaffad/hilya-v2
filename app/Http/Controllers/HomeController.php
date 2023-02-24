<?php

namespace App\Http\Controllers;

use App\Http\Controllers\LandingPage\PreorderProduct;
use App\Http\Controllers\LandingPage\PromoProduct;
use App\Http\Controllers\LandingPage\ReadyProduct;
use App\Http\Controllers\LandingPage\Slide;
use App\Models\ProductBrand;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $slides = Slide::getSlides()->limit(3)->get();
        $productBrands = ProductBrand::all();
        $readyProducts = ReadyProduct::getReadyProducts()->limit(8)->get();
        $promoProducts = PromoProduct::getPromoProducts()->limit(8)->get();
        $preorderProducts = PreorderProduct::getPreorderProducts()->limit(8)->get();
        return response()->json(compact('slides', 'productBrands', 'readyProducts', 'promoProducts', 'preorderProducts'));
    }
}
