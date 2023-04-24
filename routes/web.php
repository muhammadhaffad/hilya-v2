<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\ProductController;
use App\Models\Order;

/* use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Models\ProductItem;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PreorderProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\PromoProductController;
use App\Http\Controllers\ReadyProductController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BrandProductController; */

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

Route::get('/customer/dashboard', [DashboardController::class, 'index'])->name('customer.dashboard');

Route::get('/customer/orders', [OrderController::class, 'index'])->name('customer.orders');
Route::get('/customer/orders/{code}', [OrderController::class, 'show'])->name('customer.orders.show');
Route::post('/customer/orders/{code}/delivered', [OrderController::class, 'setDelivered'])->name('customer.orders.set-delivered');

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

Route::view('/admin/dashboard', 'v2.admin.dashboard.index')->name('admin.dashboard');

Route::view('/admin/account', 'v2.admin.account.index')->name('admin.account');
Route::put('/admin/account', [AdminAccountController::class, 'update'])->name('admin.account.update');
Route::put('/admin/change-password', [AdminAccountController::class, 'changePassword'])->name('admin.account.change-password');

Route::get('/admin/products', [AdminProductController::class, 'index'])->name('admin.product');
Route::get('/admin/products/data', [AdminProductController::class, 'dataProduct'])->name('admin.product.data');
Route::get('/admin/products/create', [AdminProductController::class, 'create'])->name('admin.product.create');
Route::post('/admin/products/store', [AdminProductController::class, 'store'])->name('admin.product.store');
Route::put('/admin/products/{id}', [AdminProductController::class, 'update'])->name('admin.product.update');
Route::delete('/admin/products/{id}', [AdminProductController::class, 'destroy'])->name('admin.product.delete');
Route::get('/admin/products/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.product.edit');

Route::get('/admin/brands', [BrandController::class, 'index'])->name('admin.brand');
Route::get('/admin/brands/data', [BrandController::class, 'dataBrand'])->name('admin.brand.data');
Route::get('/admin/brands/create', [BrandController::class, 'create'])->name('admin.brand.create');
Route::post('/admin/brands/store', [BrandController::class, 'store'])->name('admin.brand.store');
Route::put('/admin/brands/{id}', [BrandController::class, 'update'])->name('admin.brand.update');
Route::delete('/admin/brands/{id}', [BrandController::class, 'destroy'])->name('admin.brand.delete');
Route::get('/admin/brands/{id}/edit', [BrandController::class, 'edit'])->name('admin.brand.edit');

Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.order');
Route::get('/admin/orders/data', [AdminOrderController::class, 'dataOrder'])->name('admin.order.data');
Route::get('/admin/orders/{code}', [AdminOrderController::class, 'show'])->name('admin.order.show');
Route::post('/admin/orders/{code}/processing', [AdminOrderController::class, 'setProcessing'])->name('admin.order.set-processing');
Route::post('/admin/orders/{code}/shipping', [AdminOrderController::class, 'setShipping'])->name('admin.order.set-shipping');
Route::post('/admin/orders/{code}/success', [AdminOrderController::class, 'setSuccess'])->name('admin.order.set-success');

Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

Route::post('payment-callback', [PaymentCallbackController::class, 'callback'])->withoutMiddleware('csrf');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products/ready', [ProductController::class, 'ready'])->name('product.ready');
Route::get('/products/promo', [ProductController::class, 'promo'])->name('product.promo');
Route::get('/products/preorder', [ProductController::class, 'preorder'])->name('product.preorder');
Route::get('/products/{product:id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/search-products', [ProductController::class, 'searchProducts'])->name('search-products');
Route::post('/products/{product:id}/add-to-cart', [ProductController::class, 'addToCart'])->middleware('customer')->name('product.add-to-cart');

/* Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [ProductSearchController::class, 'search']);
Route::get('/brands/{brand:slug}/products', [BrandProductController::class, 'index']);
Route::get('/products/ready', [ReadyProductController::class, 'index']);
Route::get('/products/promo', [PromoProductController::class, 'index']);
Route::get('/products/preorder', [PreorderProductController::class, 'index']);
Route::get('/sign-in', fn () => 'sign in page')->name('sign_in');
Route::post('/sign-in', [AuthenticationController::class, 'signIn'])->middleware('guest');
Route::get('/sign-up', fn () => 'sign up page');
Route::post('/sign-up', [CustomerController::class, 'signUp'])->middleware('guest');
Route::post('/sign-out', [AuthenticationController::class, 'signOut'])->middleware('auth'); */