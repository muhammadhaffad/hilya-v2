<?php

namespace App\Providers;

use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductRepositoryImplement;
use App\Services\Account\AccountService;
use App\Services\Account\AccountServiceImplement;
use App\Services\Address\AddressService;
use App\Services\Address\AddressServiceImplement;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceImplement;
use App\Services\Cart\CartService;
use App\Services\Cart\CartServiceImplement;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\CheckoutServiceImplement;
use App\Services\Order\OrderService;
use App\Services\Order\OrderServiceImplement;
use App\Services\Payment\MidtransPaymentServiceImplement;
use App\Services\Payment\PaymentService;
use App\Services\Product\ProductService;
use App\Services\Product\ProductServiceImplement;
use App\Services\ProductBrand\ProductBrandService;
use App\Services\ProductBrand\ProductBrandImplement;
use App\Services\ShippingCost\RajaOngkirShippingCostServiceImplement;
use App\Services\ShippingCost\ShippingCostService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProductService::class, ProductServiceImplement::class);
        $this->app->bind(OrderService::class, OrderServiceImplement::class);
        $this->app->bind(ProductBrandService::class, ProductBrandImplement::class);
        $this->app->bind(ShippingCostService::class, RajaOngkirShippingCostServiceImplement::class);
        $this->app->bind(PaymentService::class, MidtransPaymentServiceImplement::class);
        $this->app->bind(CartService::class, CartServiceImplement::class);
        $this->app->bind(CheckoutService::class, CheckoutServiceImplement::class);
        $this->app->bind(AddressService::class, AddressServiceImplement::class);
        $this->app->bind(AccountService::class, AccountServiceImplement::class);
        $this->app->bind(AuthService::class, AuthServiceImplement::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
