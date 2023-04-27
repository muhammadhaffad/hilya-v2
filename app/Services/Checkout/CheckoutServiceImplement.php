<?php

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\ProductItem;
use App\Models\ProductOrigin;
use App\Models\ShippingAddress;
use App\Services\Payment\PaymentService;
use App\Services\ShippingCost\ShippingCostService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CheckoutServiceImplement implements CheckoutService
{
    protected $shippingCostService;
    protected $paymentService;
    public function __construct(ShippingCostService $shippingCostService, PaymentService $paymentService)
    {
        $this->shippingCostService = $shippingCostService;
        $this->paymentService = $paymentService;
    }

    private function calcDiscount(): int
    {
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])->first()->load('orderItems.productItem.product');
        return (function() use ($checkout) {
            $total = 0;
            foreach ($checkout->orderItems as $orderItem) {
                if ($orderItem->productItem->product->ispromo) {
                    $total += $orderItem->qty * (int)($orderItem->productItem->price*$orderItem->productItem->discount/100);
                }
            }
            return $total;
        })();
    }

    /**
     * checkUnvailableProducts
     *
     * @param  Builder $query
     * @return array
     */
    public function checkUnvailableProducts(): mixed
    {
        /* TODO : Optimasi pengecekan untuk product bundle */
        DB::beginTransaction();
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])->first()->load([
            'orderItems' => [
                'productItem' => [
                    'productOrigins',
                    'product' => [
                        'productBrand',
                        'productImage'
                    ]
                ],
                'product' => [
                    'productItems'
                ]
            ],
            'payment',
            'shipping' => [
                'shippingAddress'
            ],
            'productItems'
        ]);
        $unavailableProducts = [];
        foreach ($checkout->orderItems as $orderItem ) {
            if ($orderItem->productItem->is_bundle) {
                foreach ($orderItem->productItem->productOrigins as $productOrigin) {
                    $productOrigin->decrement('stock', $orderItem->qty);
                }
            } 
        }
        foreach ($checkout->orderItems as $orderItem ) {
            foreach ($orderItem->product->productItems as $productItem) {
                if ($productItem->is_bundle) {
                    $productItem->update([
                        'stock' => $productItem->productOrigins()->min('stock')
                    ]);
                    if ($productItem->stock < 0) {
                        $unavailableProducts['checkout.' . $orderItem->id] = ['Produk item kosong atau jumlah pesanan melebihi stok'];
                    }
                }
            }
            if (!$orderItem->productItem->is_bundle) {
                $orderItem->productItem->decrement('stock', $orderItem->qty);
                if ($productItem->stock < 0) {
                    $unavailableProducts['checkout.' . $orderItem->id] = ['Produk item kosong atau jumlah pesanan melebihi stok'];
                }
            }
        }
        DB::rollBack();
        if ($unavailableProducts)
            return [
                'code' => 422,
                'message' => 'Checkout item tidak valid',
                'errors' => $unavailableProducts
            ];
        else
            return false;
    }

    public function getCheckoutItems(): array
    {
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])->first()
        ?->load([
                'orderItems:id,order_id,product_item_id,qty',
                'orderItems.productItem:id,product_id,gender,age,size,color,price,weight,note_bene,stock,is_bundle,discount',
                'orderItems.productItem.product:id,product_brand_id,name,availability,ispromo',
                'orderItems.productItem.product.productImage:id,product_images.product_id,image',
                'orderItems.productItem.product.productBrand:id,name,image',
                'shipping'
            ]);
        $checkout['total_discount'] = $this->calcDiscount();
        if ($checkout) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan checkout items',
                'data' => $checkout
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
    }

    public function getAddresses(): array
    {
        $addresses = ShippingAddress::where([['user_id', auth()->user()->id]])->get();
        if ($addresses) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan alamat',
                'data' => $addresses
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
    }
    /**
     * hasNoCheckout
     *
     * @return mixed
     */
    public function hasNoCheckout(): mixed
    {
        if (!Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])->exists())
            return array(
                'code' => 302,
                'message' => 'Tidak ada item yang di-checkout',
            );
        return false;
    }
    /**
     * changeAddressShipping
     *
     * @param  int $shippingAddressId
     * @return array
     */
    public function changeAddressShipping(array $attr): array
    {
        $validator = Validator::make($attr, [
            'shipping_address_id' => [
                'required',
                'numeric',
                Rule::exists('shipping_addresses', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->user()->id);
                })
            ], [
                'required' => 'Data :attribute wajib diisi',
                'numeric' => 'Data :attribute wajib bernilai angka',
                'exists' => 'Data dengan id :input tidak ada'
            ], [
                'shipping_address_id' => 'id alamat pengiriman'
            ]
        ]);
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $validator->errors()
            ];
        }
        DB::beginTransaction();
        try {
            Order::where('user_id', auth()->user()->id)->where('status', 'checkout')->first()->shipping()
                ->update(['shipping_address_id' => $attr['shipping_address_id']]);
            DB::commit();
            return array(
                'code' => 204,
                'message' => 'Alamat berhasil diubah'
            );
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function getAllShippingCost(): array
    {
            // return [
            //     'code' => 200,
            //     'message' => 'Sukses mendapatkan harga ongkir',
            //     'data' => [
            //         [
            //             'courier' => 'Jalur Nugraha Ekakurir (JNE)', 
            //             'services' => ['OKE' => 10000,'REG' => 20000]
            //         ], [
            //             'courier' => 'POS Indonesia', 
            //             'services' => ['POS Reguler' => 7000,'POS Kargo' => 3000]
            //         ]
            //     ]
            // ];
        try {
            $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
            $address = ShippingAddress::where('user_id', auth()->user()->id);
            $address = $address->clone()->find($checkout->first()->shipping()->first()->shipping_address_id);
            $totalWeight = $checkout->first()->totalweight;
            $jne = $this->shippingCostService->getCosts(222, $address->city_id, $totalWeight, 'jne');
            $pos = $this->shippingCostService->getCosts(222, $address->city_id, $totalWeight, 'pos');
            $costs = array_merge([$jne], [$pos]);
            if ($costs) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan harga ongkir',
                    'data' => $costs
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }    
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * placeOrder
     *
     * @param  array $attr
     * @return array
     */
    public function placeOrder(array $attr): array
    {
        $validator = Validator::make($attr, [
            'courier' => 'required',
            'service' => 'required',
            'bank' => 'required'
        ], [
            'required' => 'Data :attribute wajib diisi'
        ],[
            'courier' => 'kurir',
            'service' => 'layanan'
        ]);
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid',
                'bag' => 'formErrors',
                'errors' => $validator->errors()
            ];
        }
        $courier = $attr['courier'];
        $service = $attr['service'];
        $bank = $attr['bank'];
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
        $checkoutInformation = $checkout->first()->load([
            'orderItems' => [
                'productItem' => [
                    'productOrigins',
                    'product' => [
                        'productBrand',
                        'productImage'
                    ]
                ],
                'product' => [
                    'productItems'
                ]
            ],
            'payment',
            'shipping' => [
                'shippingAddress'
            ],
            'productItems'
        ]);
        $cityId = $checkoutInformation->shipping->shippingAddress->city_id;
        $totalWeight = $checkoutInformation->totalweight;
        $cost = $this->shippingCostService->getCosts(222, $cityId, $totalWeight, $courier);
        if (!isset($cost['services'][$service])) {
            return [
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid',
                'bag' => 'shippingErrors',
                'errors' => [
                    'courier' => ['Kurir atau layanan tidak didukung']
                ]
            ];
        }
        DB::beginTransaction();
        try {
            $shippingCost = $cost['services'][$service];
            $grandTotal = $checkoutInformation->subtotal + $shippingCost - $this->calcDiscount();
            $transaction = $this->paymentService->sendTransaction($checkoutInformation, $shippingCost, $grandTotal, $bank);
            if ($transaction['status_code'] == 201) {
                if ($transaction['fraud_status'] === 'accept') {
                    $checkoutInformation->shipping->update([
                        'courier' => $cost['courier'],
                        'service' => $service,
                        'shippingcost' => $shippingCost
                    ]);
                    $checkoutInformation->update([
                        'grandtotal' => $grandTotal
                    ]);
                    $checkoutInformation->payment()->create([
                        'order_code' => $transaction['order_id'],
                        'bank' => $bank,
                        'vanumber' => $transaction['va_numbers'][0]['va_number'],
                        'amount' => $transaction['gross_amount'],
                        'status' => $transaction['transaction_status'],
                        'transactiontime' => $transaction['transaction_time']
                    ]);
                    foreach ($checkoutInformation->orderItems as $orderItem ) {
                        if ($orderItem->productItem->is_bundle) {
                            foreach ($orderItem->productItem->productOrigins as $productOrigin) {
                                $productOrigin->decrement('stock', $orderItem->qty);
                            }
                        } 
                    }
                    foreach ($checkoutInformation->orderItems as $orderItem ) {
                        foreach ($orderItem->product->productItems as $productItem) {
                            if ($productItem->is_bundle) {
                                $productItem->update([
                                    'stock' => $productItem->productOrigins()->min('stock')
                                ]);
                            }
                        }
                        if (!$orderItem->productItem->is_bundle) {
                            $orderItem->productItem->decrement('stock', $orderItem->qty);
                        }
                    }
                    /* foreach ($checkoutInformation->orderItems as $orderItem ) {
                        if ($orderItem->productItem->is_bundle) {
                            $orderItem->productItem->update([
                                'stock' => $orderItem->productItem->productOrigins->min('stock')
                            ]);
                        } else {
                            $orderItem->productItem->decrement('stock', $orderItem->qty);
                        }
                    } */
                    $productItems = $checkoutInformation->productItems
                        ->map(fn($item) => ['id'=>$item->id, 'price'=>$item->price, 'discount'=>$item->discount])
                        ->toArray();
                    $shippingAddress = $checkoutInformation->shipping->shippingAddress;
                    $checkoutInformation->update([
                        'status' => $transaction['transaction_status'],
                        'custom_properties' => ['product_items' => $productItems, 'shipping_address' => $shippingAddress]
                    ]);
                    DB::commit();
                    return [
                        'code' => '201',
                        'message' => 'Berhasil membuat pesanan, silahkan melakukan pembayaran',
                        'data' => [
                            'code' => $transaction['order_id']
                        ]
                    ];
                } else {
                    return [
                        'code' => 500,
                        'message' => 'Pembayaran fraud, silahkan coba dengan bank lain'
                    ];
                }
            } else {
                return [
                    'code' => 422,
                    'message' => 'Data yang diberikan tidak valid',
                    'bag' => 'transactionErrors',
                    'errors' => [
                        'bank' => ['Terjadi kesalahan transaksi']
                    ]
                ];
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * backToCart
     *
     * @return array
     */
    public function backToCart(): array
    {
        DB::beginTransaction();
        try {
            Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])
            ->update(['status' => 'cart']);
            DB::commit();
            return [
                'code' => 204,
                'message' => 'Sukses kembali ke keranjang'
            ];
        } catch (\Exception $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
