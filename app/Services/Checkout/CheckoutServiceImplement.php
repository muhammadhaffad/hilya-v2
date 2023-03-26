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
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
        $checkoutItems = $checkout->join('order_items', 'order_id', 'orders.id')
            ->join('product_items', function ($join) {
                $join->on('product_items.id', '=', 'order_items.product_item_id')
                    ->on('product_items.stock', '<', 'order_items.qty');
            })->get()
            ->mapWithKeys(fn ($item) => ['checkout.' . $item->id => ['Produk item kosong atau jumlah pesanan melebihi stok']])
            ->toArray();
        if ($checkoutItems)
            return [
                'code' => 422,
                'message' => 'Checkout item tidak valid',
                'errors' => $checkoutItems
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
        DB::beginTransaction();
        try {
            $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
            $address = ShippingAddress::where('user_id', auth()->user()->id);
            $address = $address->clone()->find($checkout->first()->shipping()->first()->shipping_address_id);
            $totalWeight = $checkout->first()->totalweight;
            $cost = $this->shippingCostService->getCosts(222, $address->city_id, $totalWeight, $courier);
            if (@$cost['services'][$service]) {
                $checkout->first()->shipping()->update([
                    'courier' => $cost['courier'],
                    'service' => $service,
                    'shippingcost' => $cost['services'][$service]
                ]);
            } else {
                DB::rollBack();
                return [
                    'code' => 422,
                    'message' => 'Data yang diberikan tidak valid',
                    'bag' => 'shippingErrors',
                    'errors' => [
                        'courier' => ['Kurir atau layanan tidak didukung']
                    ]
                ];
            }
            $checkout->clone()->update([
                'grandtotal' => $checkout->first()->subtotal + $cost['services'][$service] - $this->calcDiscount()
            ]);
            $transaction = $this->paymentService->sendTransaction($bank);
            if ($transaction['status_code'] != 201) {
                DB::rollBack();
                return [
                    'code' => 422,
                    'message' => 'Data yang diberikan tidak valid',
                    'bag' => 'transactionErrors',
                    'errors' => [
                        'bank' => ['Terjadi kesalahan transaksi']
                    ]
                ];
            }
            if ($transaction['fraud_status'] === 'accept') {
                $checkout->first()->payment()->create([
                    'bank' => $bank,
                    'vanumber' => $transaction['va_numbers'][0]['va_number'],
                    'amount' => $transaction['gross_amount'],
                    'status' => $transaction['transaction_status'],
                    'transactiontime' => $transaction['transaction_time']
                ]);
                $codeOrder = $checkout->first()->code;
                ProductOrigin::whereIn('id', [1,2])->update(['stock' => 4]);
                ProductItem::where('id', 1)->update(['stock'=> 4]);
                // $checkout = Order::where('code', $codeOrder);
                // $orderItems = $checkout->first()->load('orderItems.productItem.productOrigins')->orderItems;
                // foreach ($orderItems as $orderItem ) {
                //     if ($orderItem->productItem->is_bundle) {
                //         $productOriginIds = $orderItem->productItem->productOrigins->pluck('id');
                //         ProductOrigin::whereIn('id', $productOriginIds)->decrement('stock', $orderItem->qty);
                //     } 
                // }
                // $orderItems = $checkout->first()->load('orderItems.productItem.productOrigins')->orderItems;
                // foreach ($orderItems as $orderItem ) {
                //     if ($orderItem->productItem->is_bundle) {
                //         $orderItem->productItem->update([
                //             'stock' => $orderItem->productItem->productOrigins->min('stock')
                //         ]);
                //     } else {
                //         $orderItem->productItem->decrement('stock', $orderItem->qty);
                //     }
                // }
                $productsPromo = $checkout->first()->productItems()->whereHas('product', fn($q) => $q->where('ispromo',1))->get(['product_items.id','price', 'discount'])->toJson();
                /* tidak membuat kondisi where status = checkout berubah menjadi status = pending*/
                $checkout->clone()->update([
                    'status' => $transaction['transaction_status'],
                    'custom_properties' => $productsPromo
                ]);
                DB::commit();
                return [
                    'code' => '201',
                    'message' => 'Berhasil membuat pesanan, silahkan melakukan pembayaran',
                    'data' => [
                        'code' => $codeOrder
                    ]
                ];
            } else {
                DB::rollBack();
                return array(
                    'code' => 500,
                    'message' => 'Pembayaran fraud, silahkan coba dengan bank lain'
                );
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
