<?php

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\ShippingAddress;
use App\Services\Payment\PaymentService;
use App\Services\ShippingCost\ShippingCostService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CheckoutServiceImplement implements CheckoutService
{
    protected $shippingCostService;
    protected $paymentService;
    public function __construct(ShippingCostService $shippingCostService, PaymentService $paymentService)
    {
        $this->shippingCostService = $shippingCostService;
        $this->paymentService = $paymentService;
    }
    /**
     * checkUnvailableProducts
     *
     * @param  Builder $query
     * @return array
     */
    public function checkUnvailableProducts(Builder $query): array
    {
        $q = clone $query;
        return $q->join('order_details', 'order_id', 'orders.id')
            ->join('product_details', function ($join) {
                $join->on('product_details.id', '=', 'order_details.product_detail_id')
                    ->on('product_details.stock', '<', 'order_details.qty');
            })->get()
            ->mapWithKeys(fn ($item, $key) => ["product_details.$item->product_detail_id" => "Produk sudah habis"])
            ->toArray();
    }
    /**
     * hasNoCheckout
     *
     * @return array
     */
    public function hasNoCheckout(): array
    {
        if (!Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])->exists())
            return array(
                'code' => 404,
                'data' => array(),
            );
        return array();
    }
    /**
     * changeAddressShipping
     *
     * @param  int $shippingAddressId
     * @return array
     */
    public function changeAddressShipping(int $shippingAddressId): array
    {
        $hasNoCheckout = $this->hasNoCheckout();
        if ($hasNoCheckout) {
            return $hasNoCheckout;
        }
        if (!ShippingAddress::where('user_id', auth()->user()->id)->find($shippingAddressId))
            return array(
                'code' => 404,
                'data' => array(),
            );
        try {
            Order::where('user_id', auth()->user()->id)->where('status', 'checkout')->first()->shipping()
                ->update(['shipping_address_id' => $shippingAddressId]);
            return array(
                'code' => 200,
                'data' => array(
                    'url' => back()->getTargetUrl(),
                    'message' => 'Alamat berhasil diubah'
                )
            );
        } catch (Exception $e) {
            return array(
                'code' => 500,
                'message' => $e
            );
        }
    }
    public function getAllShippingCost(): array
    {
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
        $address = ShippingAddress::where('user_id', auth()->user()->id);
        $address = $address->clone()->selected()->first() ?: $address->clone()->primary()->first();
        $totalWeight = $checkout->first()->totalweight;
        $jne = $this->shippingCostService->getCosts(222, $address->city_id, $totalWeight, 'jne');
        $pos = $this->shippingCostService->getCosts(222, $address->city_id, $totalWeight, 'pos');
        $costs = array_merge([$jne], [$pos]);
        return array(
            'code' => $costs ? 200 : 400,
            'data' => $costs
        );
    }
    /**
     * placeOrder
     *
     * @param  string $courier
     * @param  string $service
     * @param  string $bank
     * @return array
     */
    public function placeOrder(string $courier, string $service, string $bank): array
    {
        $hasNoCheckout = $this->hasNoCheckout();
        if ($hasNoCheckout) {
            return $hasNoCheckout;
        }
        $checkout = Order::where([['user_id', auth()->user()->id], ['status', 'checkout']]);
        $productSoldOut = $this->checkUnvailableProducts($checkout);
        if ($productSoldOut)
            return array(
                'code' => 400,
                'redirect' => array(
                    'url' => back()->getTargetUrl()
                ),
                'errors' => $productSoldOut
            );
        $address = ShippingAddress::where('user_id', auth()->user()->id);
        $address = $address->clone()->selected()->first() ?: $address->clone()->primary()->first();
        $totalWeight = $checkout->first()->totalweight;
        $cost = $this->shippingCostService->getCosts(222, $address->city_id, $totalWeight, $courier);
        if (@$cost['services'][$service]) {
            $checkout->first()->shipping()->update([
                'courier' => $cost['courier'],
                'service' => $service,
                'shippingcost' => $cost['services'][$service]
            ]);
        } else {
            return array(
                'code' => 400,
                'redirect' => array(
                    'url' => back()->getTargetUrl()
                ),
                'errors' => array(
                    'courier' => 'Kurir atau service tidak didukung'
                )
            );
        }
        $checkout->clone()->update([
            'grandtotal' => $checkout->first()->subtotal + $cost['services'][$service]
        ]);
        $transaction = $this->paymentService->sendTransaction($bank);
        if (!$transaction)
            return array(
                'code' => 400,
                'redirect' => array(
                    'url' => back()->getTargetUrl()
                ),
                'errors' => array(
                    'bank' => 'Hanya dapat melalui bank BNI dan BRI'
                )
            );
        if ($transaction['fraud_status'] === 'accept') {
            $checkout->first()->payment()->create([
                'bank' => $bank,
                'vanumber' => $transaction['va_numbers'][0]['va_number'],
                'amount' => $transaction['gross_amount'],
                'status' => $transaction['transaction_status'],
                'transactiontime' => $transaction['transaction_time']
            ]);
            foreach ($checkout->first()->orderDetails()->get()->all() as $item) {
                $item->productDetail()->decrement('stock', $item->qty);
            }
            $codeOrder = $checkout->first()->code;
            /* tidak membuat kondisi where status = checkout berubah menjadi status = pending*/
            $checkout->clone()->update([
                'status' => $transaction['transaction_status']
            ]);
            return array(
                'code' => 200,
                'data' => array(
                    'url' => url('customer/orders/' . $codeOrder),
                    'message' => 'Pesanan sukses dibuat, silahkan melakukan pembayaran'
                )
            );
        } else {
            return array(
                'code' => 500,
                'message' => 'Pembayaran fraud, silahkan coba dengan bank lain'
            );
        }
    }
    /**
     * backToCart
     *
     * @return array
     */
    public function backToCart(): array
    {
        $hasNoCheckout = $this->hasNoCheckout();
        if ($hasNoCheckout) {
            return $hasNoCheckout;
        }
        if (Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])->update(['status' => 'cart'])) {
            return array(
                'code' => 302,
                'redirect' => array(
                    'url' => url('customer/cart'),
                    'message' => null
                )
            );
        } else {
            return array(
                'code' => 500
            );
        }
    }
}
