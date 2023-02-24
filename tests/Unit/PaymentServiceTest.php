<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\Payment\MidtransPaymentServiceImplement;
use App\Services\Payment\PaymentService;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;
    protected $paymentService;
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->app->bind(PaymentService::class, MidtransPaymentServiceImplement::class);
        $this->paymentService = $this->app->make(PaymentService::class);
    }
    public function calcSubTotal(Builder $query): int
    {
        $q = clone $query;
        return $q->join('order_items', 'order_id', 'orders.id')
            ->join('product_items', function ($join) {
                $join->on('product_items.id', '=', 'order_items.product_item_id');
            })->select(DB::raw('sum(order_items.qty * product_items.price) as subtotal'))->first()->subtotal;
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_payment_service()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status' => 'checkout']);
        $checkout = Order::where([['user_id', 1], ['status', 'checkout']]);
        $grandtotal = $this->calcSubTotal($checkout) + 2000;
        $checkout->update(['grandtotal' => $grandtotal]);
        dump($checkout->get());
        dump($this->paymentService->sendTransaction('bri'));
    }
}
