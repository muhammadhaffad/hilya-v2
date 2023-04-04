<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BrandProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
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

Route::get('/customer/dashboard', [DashboardController::class, 'index'])->name('customer.dashboard');

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

Route::get('/customer/region/', [AddressController::class, 'provinces']);
Route::get('/customer/region/{provinceId}', [AddressController::class, 'cities']);
Route::get('/customer/region/{provinceId}/{cityId}', [AddressController::class, 'subdistricts']);
Route::get('/customer/address-book', [AddressController::class, 'index'])->name('customer.address-book');
Route::get('/customer/address-book/create', [AddressController::class, 'create'])->name('customer.address-book.create');
Route::post('/customer/address-book/create', [AddressController::class, 'create'])->name('customer.address-book.create');
Route::get('/customer/address-book/{addressId}/edit', [AddressController::class, 'edit'])->name('customer.address-book.edit');
Route::put('/customer/address-book/{addressId}/update', [AddressController::class, 'update'])->name('customer.address-book.update');
Route::delete('/customer/address-book/{addressId}/delete', [AddressController::class, 'destroy'])->name('customer.address-book.destroy');
Route::put('/customer/address-book/{addressId}/select', [AddressController::class, 'select'])->name('customer.address-book.select');

Route::get('/customer/account', [AccountController::class, 'index'])->name('customer.account.index');
Route::put('/customer/account/update', [AccountController::class, 'update'])->name('customer.account.update');
Route::put('/customer/account/change-password', [AccountController::class, 'changePassword'])->name('customer.account.change-password');

Route::post('payment-callback', [PaymentCallbackController::class, 'callback'])->withoutMiddleware('csrf');

Route::get('/test', function () {
    auth()->loginUsingId(1);
    $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']]);
    $cart->first()->shipping()->create(
        ['shipping_address_id' => 1]
    );
    return '';
});
