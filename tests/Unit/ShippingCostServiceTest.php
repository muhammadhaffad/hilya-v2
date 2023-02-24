<?php

namespace Tests\Unit;

use App\Services\ShippingCost\RajaOngkirShippingCostServiceImplement;
use App\Services\ShippingCost\ShippingCostService;
use Tests\TestCase;

class ShippingCostServiceTest extends TestCase
{
    protected $shippingCostService;
    public function setUp(): void
    {
        parent::setUp();
        $this->app->bind(ShippingCostService::class, RajaOngkirShippingCostServiceImplement::class);

        $this->shippingCostService = $this->app->make(ShippingCostService::class);
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        dump($this->shippingCostService->getCosts(222, 222, 1000, 'jne'));
        $this->assertTrue(true);
    }
}
