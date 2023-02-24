<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\CheckoutServiceImplement;
use App\Services\Payment\MidtransPaymentServiceImplement;
use App\Services\Payment\PaymentService;
use App\Services\ShippingCost\RajaOngkirShippingCostServiceImplement;
use App\Services\ShippingCost\ShippingCostService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $checkoutService;
    
    public function setUp() : void
    {
        parent::setUp();
        $this->seed();
        $this->app->bind(ShippingCostService::class, RajaOngkirShippingCostServiceImplement::class);
        $this->app->bind(PaymentService::class, MidtransPaymentServiceImplement::class);
        $this->app->bind(CheckoutService::class, CheckoutServiceImplement::class);

        $this->checkoutService = $this->app->make(CheckoutService::class);
    }

    public function calcSubTotal(Builder $query): int
    {
        $q = clone $query;
        return $q->join('order_details', 'order_id', 'orders.id')
            ->join('product_details', function ($join) {
                $join->on('product_details.id', '=', 'order_details.product_detail_id');
            })->select(DB::raw('sum(order_details.qty * product_details.price) as subtotal'))->first()->subtotal;
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_check_change_address_shipping()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'checkout']);

        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
        $checkout->first()->orderDetails()->first()->update(['qty' => 8]);
        $response = $this->checkoutService->changeAddressShipping(2);
        dump($response);
        $this->assertContainsEquals(200, $response);
    }

    public function test_check_change_address_shipping_not_found()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'checkout']);

        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
        $checkout->first()->orderDetails()->first()->update(['qty' => 8]);
        $response = $this->checkoutService->changeAddressShipping(200);
        dump($response);
        $this->assertContainsEquals(404, $response);
    }

    public function test_place_orde()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'checkout']);
        auth()->user()->shippingAddresses()->first()->update(['type'=>'primary', 'city_id' => 222]);
        
        $checkout = Order::where([['user_id', 1], ['status', 'checkout']]);
        $grandtotal = $this->calcSubTotal($checkout);
        $checkout->update(['subtotal' => $grandtotal]);
        
        $response = $this->checkoutService->placeOrder('jne', 'CTC', 'bri');
        dump($response);
        $this->assertContainsEquals(200, $response);
    }

    public function test_place_orde_unsupported_courier_or_service()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'checkout']);
        auth()->user()->shippingAddresses()->first()->update(['type'=>'primary']);
        
        $checkout = Order::where([['user_id', 1], ['status', 'checkout']]);
        $grandtotal = $this->calcSubTotal($checkout);
        $checkout->update(['subtotal' => $grandtotal]);
        
        $response = $this->checkoutService->placeOrder('jne', 'CTC', 'bri');
        dump($response);
        $this->assertContainsEquals(400, $response);
    }

    public function test_place_orde_unsupported_bank()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'checkout']);
        auth()->user()->shippingAddresses()->first()->update(['type'=>'primary', 'city_id' => 222]);
        
        $checkout = Order::where([['user_id', 1], ['status', 'checkout']]);
        $grandtotal = $this->calcSubTotal($checkout);
        $checkout->update(['subtotal' => $grandtotal]);
        
        $response = $this->checkoutService->placeOrder('jne', 'CTC', 'bca');
        dump($response);
        $this->assertContainsEquals(400, $response);
    }

    public function test_all_shipping_cost()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'checkout']);
        auth()->user()->shippingAddresses()->first()->update(['type'=>'primary', 'city_id' => 222]);
        
        $response = $this->checkoutService->getAllShippingCost();
        dump($response);
        $this->assertIsArray($response);
    }

    public function test_update()
    {
        auth()->loginUsingId(2);
        auth()->user()->orders()->first()->update(['status'=>'checkout']);
        
        $checkout = Order::where([['user_id', 2], ['status', 'checkout']]);
        $checkoutId = $checkout->first()->id;
        dump($checkout->get());
        $checkout->update(['status'=>'pending']);
        $checkout = Order::where('id', $checkoutId);
        dump($checkout->get());
        $this->assertTrue(true);
    }
}
