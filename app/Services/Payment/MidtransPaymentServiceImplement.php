<?php

namespace App\Services\Payment;

use App\Models\Order;
use Exception;
use GuzzleHttp\Client;

class MidtransPaymentServiceImplement implements PaymentService
{
    private function calcDiscount(): int
    {
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])->first()->load('orderItems.productItem.product');
        return -(function() use ($checkout) {
            $total = 0;
            foreach ($checkout->orderItems as $orderItem) {
                if ($orderItem->productItem->product->ispromo) {
                    $total += $orderItem->qty * (int)($orderItem->productItem->price*$orderItem->productItem->discount/100);
                }
            }
            return $total;
        })();
    }

    public function sendTransaction(string $bank): array
    {
        if (!in_array($bank, ['bni', 'bri']))
            return [];
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
        // $itemDetails = $checkout->first()->orderItems()->with([
        //     'productItem:id,product_id,gender,age,size,price,note_bene,is_bundle',
        //     'productItem.product:id,product_brand_id,name',
        //     'productItem.product.productBrand:id,name'
        // ])->get(['qty', 'product_item_id'])->map(function ($item, $key) {
        //     $brandName = $item->productItem->product->productBrand->name;
        //     $productName = $item->productItem->product->name;
        //     $gender = $item->productItem->gender;
        //     $age = $item->productItem->age;
        //     $size = $item->productItem->size;
        //     $model = $item->productItem->model;
        //     return [
        //         'id' => 'product-detail.' . $item->productItem->id,
        //         'price' => (int) $item->productItem->price,
        //         'quantity' => (int) $item->qty,
        //         'name' => "($brandName) $productName $gender $age ($size) $model"
        //     ];
        // })->toArray();
        // $itemDetails[] = [
        //     'id' => 'shipping-cost',
        //     'price' => $checkout->first()->shipping()->first()->shippingcost,
        //     'quantity' => 1,
        //     'name' => 'biaya ongkir'
        // ];
        // $itemDetails[] = [
        //     'id' => 'D01',
        //     'price' => $this->calcDiscount(),
        //     'quantity' => 1,
        //     'name' => 'Discount'
        // ];
        // $transactionDetails = [
        //     'order_id' => $checkout->first()->code,
        //     'gross_amount' => (int) $checkout->first()->grandtotal
        // ];

        $itemDetails[] = [
            'id' => 'D01',
            'price' => 1000,
            'quantity' => 1,
            'name' => 'kontolll'
        ];
        $transactionDetails = [
            'order_id' => \Str::uuid()->toString(),
            'gross_amount' => 1000
        ];

        $client = new Client([
            'base_uri' => env('MIDTRANS_URL'),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . env('MIDTRANS_SERVER_KEY'),
                'Content-Type' => 'application/json',
            ]
        ]);
        $data = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => $transactionDetails,
            'bank_transfer' => [
                'bank' => $bank
            ],
            'item_details' => $itemDetails
        ];
        try {
            $response = $client->request('POST', 'charge', [
                'json' => $data
            ]);
            return json_decode($response->getBody(), true);
        } catch (Exception $e) {
            throw new Exception($e->getResponse()->getBody()->getContents());
        }
        // return $data;
    }
}
