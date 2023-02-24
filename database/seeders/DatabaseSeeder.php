<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ProductItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductImage;
use App\Models\Shipping;
use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $users = User::factory(3)->create();
        $product_brands = ProductBrand::factory(5)->create();
        $users->each(function($user) {
            for ($i=0; $i < 3; $i++) { 
                $user->orders()->save(Order::factory()->make());
            }
        });
        $users->each(function($user) {
            for ($i=0; $i < 3; $i++) { 
                $user->shippingAddresses()->save(ShippingAddress::factory()->make());
            }
        });
        $orders = Order::all();
        $orders->each(function($order) {
            $shipping_address_id = $order->user()->first()->shippingAddresses()->pluck('id')->collect()->random();
            $order->shipping()->save(Shipping::factory(['shipping_address_id'=>$shipping_address_id])->make());
            $order->payment()->save(Payment::factory()->make());
        });
        Product::factory(5)->brand(1, $product_brands->count())->has(ProductImage::factory(5), 'productImages')->has(ProductItem::factory(5), 'productItems')->create();
        $users->each(function($user) {
            $user->orders()->each(function($order) {
                for ($i=0; $i < 3; $i++) { 
                    $order->orderItems()->save(OrderItem::factory()->productItem(25)->make());
                }
            });
        });
    }
}
