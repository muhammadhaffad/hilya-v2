<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BrandProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PreorderProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\PromoProductController;
use App\Http\Controllers\ReadyProductController;
use App\Http\Controllers\TestController;
use App\Models\Order;
use App\Models\ProductDetail;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|

Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [ProductSearchController::class, 'search']);
Route::get('/brands/{brand:slug}/products', [BrandProductController::class, 'index']);
Route::get('/products/ready', [ReadyProductController::class, 'index']);
Route::get('/products/promo', [PromoProductController::class, 'index']);
Route::get('/products/preorder', [PreorderProductController::class, 'index']);
Route::get('/products/{product:id}', [ProductDetailController::class, 'index']);
Route::post('/products/{product:id}/add-to-cart', [ProductDetailController::class, 'addToCart'])->middleware('customer');
Route::get('/sign-in', fn () => 'sign in page')->name('sign_in');
Route::post('/sign-in', [AuthenticationController::class, 'signIn'])->middleware('guest');
Route::get('/sign-up', fn () => 'sign up page');
Route::post('/sign-up', [CustomerController::class, 'signUp'])->middleware('guest');
Route::post('/sign-out', [AuthenticationController::class, 'signOut'])->middleware('auth');
Route::get('/customer/orders', [OrderController::class, 'orderHistory']);
Route::get('/customer/orders/{code}', [OrderController::class, 'orderDetail']);
Route::get('/customer/cart', [CartController::class, 'index']);
Route::post('/customer/cart/{order_detail_id}/add', [CartController::class, 'add']);
Route::post('/customer/cart/{order_detail_id}/sub', [CartController::class, 'sub']);
Route::post('/customer/cart/{order_detail_id}/remove', [CartController::class, 'remove']);
Route::get('/customer/checkout', fn () => 'abcd')->name('checkout');
Route::get('/test', function () {
    auth()->loginUsingId(1);
    $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']]);
    $cart->first()->shipping()->create(
        ['shipping_address_id' => 1]
    );
    return '';
});
