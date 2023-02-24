<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Services\Cart\CartServiceImplement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{

    use RefreshDatabase;

    protected $cartService;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->cartService = new CartServiceImplement;
    }

    public function test_it_has_checkout()
    {
        auth()->loginUsingId(1);
        $response = $this->cartService->hasCheckout();
        $this->assertTrue(boolval($response));
        // $this->assertContainsEquals(302, $response);
    }

    public function test_it_has_unvailable_products()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        
        $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']]);
        $cart->first()->orderDetails()->first()->update(['qty' => 8]);
        $result = $this->cartService->checkUnvailableProducts($cart);
        dump($result);
        $this->assertIsArray($result);
    }

    public function test_calc_sub_total()
    {
        auth()->loginUsingId(1);

        $cart = Order::where([['user_id', auth()->user()->id], ['orders.id', 1]]);
        dump($cart->with('orderDetails:id,order_id,product_item_id,qty', 'orderDetails.productItem:id,price')->get('id')->toArray());
        $result = $this->cartService->calcSubTotal($cart);
        dump($result);
        $this->assertIsInt($result);
    }

    public function test_calc_weight_total()
    {
        auth()->loginUsingId(1);

        $cart = Order::where([['user_id', auth()->user()->id], ['orders.id', 1]]);
        dump($cart->with('orderDetails:id,order_id,product_item_id,qty', 'orderDetails.productItem:id,weight')->get('id')->toArray());
        $result = $this->cartService->calcWeightTotal($cart);
        dump($result);
        $this->assertIsInt($result);   
    }

    public function test_get_cart()
    {
        auth()->loginUsingId(1);
        $response = $this->cartService->getCart();
        dump($response);
        $this->assertContainsEquals(404, $response);
    }

    public function test_add_quantity()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $qty = OrderDetail::find(1)->qty;
        $response = $this->cartService->addQty(1);
        $qtyNew = OrderDetail::find(1)->qty;
        dump($response);
        $this->assertContainsEquals(200, $response);
        $this->assertTrue($qty+1 == $qtyNew);
    }

    public function test_add_quantity_not_found()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $qty = OrderDetail::find(1)->qty;
        $response = $this->cartService->addQty(1000);
        $qtyNew = OrderDetail::find(1)->qty;
        dump($response);
        $this->assertContainsEquals(404, $response);
    }

    public function test_add_quantity_bad_request()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $item = OrderDetail::find(1);
        $item->qty = 9999;
        $item->save();
        $response = $this->cartService->addQty(1);
        dump($response);
        $this->assertContainsEquals(400, $response);
    }

    public function test_sub_quantity()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $item = OrderDetail::find(1);
        $item->qty = 2;
        $item->save();
        $response = $this->cartService->subQty(1);
        dump($response);
        $this->assertContainsEquals(200, $response);
    }

    public function test_sub_quantity_not_found()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $qty = OrderDetail::find(1)->qty;
        $response = $this->cartService->subQty(1000);
        $qtyNew = OrderDetail::find(1)->qty;
        dump($response);
        $this->assertContainsEquals(404, $response);
    }

    public function test_sub_quantity_bad_request()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $item = OrderDetail::find(1);
        $item->qty = 1;
        $item->save();
        $response = $this->cartService->subQty(1);
        dump($response);
        $this->assertContainsEquals(400, $response);
    }

    public function test_remove_item()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $response = $this->cartService->removeItem(1);
        dump($response);
        $this->assertContainsEquals(200, $response);
    }

    public function test_remove_item_not_found()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $response = $this->cartService->removeItem(100);
        dump($response);
        $this->assertContainsEquals(404, $response);
    }

    public function test_add_to_cart_with_no_cart_before()
    {
        auth()->loginUsingId(1);
        // auth()->user()->orders()->first()->update(['status'=>'cart']);
        $product = Product::find(1);
        $response = $this->cartService->addToCart($product, 2, 1);
        $this->assertContainsEquals(200, $response);
    }
    
    public function test_add_to_cart()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $product = Product::find(1);
        $response = $this->cartService->addToCart($product, 2, 1);
        $this->assertContainsEquals(200, $response);
    }


    public function test_add_to_cart_not_found()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $product = Product::find(1);
        $response = $this->cartService->addToCart($product, 200, 1);
        $this->assertContainsEquals(404, $response);
    }

    public function test_add_to_cart_bad_request()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        $product = Product::find(1);
        $response = $this->cartService->addToCart($product, 2, 0);
        $this->assertContainsEquals(400, $response);
    }

    public function test_checkout_cart()
    {
        auth()->loginUsingId(1);
        auth()->user()->orders()->first()->update(['status'=>'cart']);
        auth()->user()->shippingAddresses()->first()->update(['type'=>'primary']);
        $response = $this->cartService->checkoutCart();
        $this->assertContainsEquals(200, $response);
    }
}
