<?php

namespace App\Services\Payment;

use App\Models\Order;
use Exception;
use GuzzleHttp\Client;

class MidtransPaymentServiceImplement implements PaymentService
{
    public function sendTransaction(string $bank): array
    {
        if (!in_array($bank, ['bni', 'bri']))
            return array();
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
        $itemDetails = $checkout->first()->orderDetails()->with([
            'productDetail:id,product_id,gender,age,size,model,fabric,price',
            'productDetail.product:id,product_brand_id,name',
            'productDetail.product.productBrand:id,name'
        ])->get(['qty', 'product_detail_id'])->map(function ($item, $key) {
            $brandName = $item->productDetail->product->productBrand->name;
            $productName = $item->productDetail->product->name;
            $gender = $item->productDetail->gender;
            $age = $item->productDetail->age;
            $size = $item->productDetail->size;
            $model = $item->productDetail->model;
            return array(
                'id' => 'product-detail.' . $item->productDetail->id,
                'price' => (int) $item->productDetail->price,
                'quantity' => (int) $item->qty,
                'name' => "($brandName) $productName $gender $age ($size) $model"
            );
        })->toArray();
        array_push($itemDetails, array(
            'id' => 'shipping-cost',
            'price' => $checkout->first()->shipping()->first()->shippingcost,
            'quantity' => 1,
            'name' => 'biaya ongkir'
        ));
        $transactionDetails = array(
            'order_id' => $checkout->first()->code,
            'gross_amount' => (int) $checkout->first()->grandtotal
        );
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
