<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BrandProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PreorderProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\PromoProductController;
use App\Http\Controllers\ReadyProductController;
use App\Http\Controllers\TestController;
use App\Models\Order;
use App\Models\ProductItem;
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
Route::get('/products/{product:id}', [ProductController::class, 'show']);
Route::post('/products/{product:id}/add-to-cart', [ProductController::class, 'addToCart'])->middleware('customer')->name('product.add-to-cart');
Route::get('/sign-in', fn () => 'sign in page')->name('sign_in');
Route::post('/sign-in', [AuthenticationController::class, 'signIn'])->middleware('guest');
Route::get('/sign-up', fn () => 'sign up page');
Route::post('/sign-up', [CustomerController::class, 'signUp'])->middleware('guest');
Route::post('/sign-out', [AuthenticationController::class, 'signOut'])->middleware('auth');

Route::get('/customer/orders', [OrderController::class, 'index'])->name('customer.orders');
Route::get('/customer/orders/{code}', [OrderController::class, 'show'])->name('customer.orders.show');

Route::get('/customer/test', [CartController::class, 'test'])->name('customer.test');
Route::get('/customer/cart', [CartController::class, 'index'])->name('customer.cart');
Route::post('/customer/cart/add', [CartController::class, 'add'])->name('customer.cart.add');
Route::post('/customer/cart/sub', [CartController::class, 'sub'])->name('customer.cart.sub');
Route::post('/customer/cart/remove', [CartController::class, 'remove'])->name('customer.cart.remove');
Route::post('/customer/cart/checkout', [CartController::class, 'checkout'])->name('customer.cart.checkout');

Route::get('/customer/checkout', [CheckoutController::class, 'index'])->name('customer.checkout');
Route::post('/customer/checkout/change-address-shipping', [CheckoutController::class, 'changeAddressShipping'])->name('customer.checkout.change-address-shipping');
Route::post('/customer/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('customer.checkout.place-order');
Route::post('/customer/checkout/back-to-cart', [CheckoutController::class, 'backToCart'])->name('customer.checkout.back-to-cart');

Route::post('payment-callback', [PaymentCallbackController::class, 'callback'])->withoutMiddleware('csrf');

Route::get('/test', function () {
    auth()->loginUsingId(1);
    $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']]);
    $cart->first()->shipping()->create(
        ['shipping_address_id' => 1]
    );
    return '';
});
