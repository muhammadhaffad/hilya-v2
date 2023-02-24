<?php

namespace App\Providers;

use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductRepositoryImplement;
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
