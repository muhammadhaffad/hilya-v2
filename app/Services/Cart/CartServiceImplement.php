<?php

namespace App\Services\Cart;

use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CartServiceImplement implements CartService
{
    /**
     * hasCheckout
     *
     * @return array
     */
    public function hasCheckout(): array
    {
        if (Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])->exists())
            return array(
                'code' => 302,
                'redirect' => array(
                    'url' => url('customer/checkout'),
                    'message' => 'Selesaikan proses checkout Anda terlebih dahulu.'
                ),
            );
        return array();
    }
    /**
     * checkUnvailableProducts
     *
     * @param  Builder $q
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
            ->mapWithKeys(fn ($item, $key) => ["product_details.$item->product_detail_id" => "Stok sudah habis atau jumlah pesanan lebih dari stok"])
            ->toArray();
    }
    /**
     * calcSubTotal
     *
     * @param  Builder $query
     * @return int
     */
    public function calcSubTotal(Builder $query): int
    {
        $q = clone $query;
        return $q->join('order_details', 'order_id', 'orders.id')
            ->join('product_details', function ($join) {
                $join->on('product_details.id', '=', 'order_details.product_detail_id');
            })->select(DB::raw('sum(order_details.qty * product_details.price) as subtotal'))->first()->subtotal;
    }
    /**
     * calcWeightTotal
     *
     * @param  Builder $query
     * @return int
     */
    public function calcWeightTotal(Builder $query): int
    {
        $q = clone $query;
        return $q->join('order_details', 'order_id', 'orders.id')
            ->join('product_details', function ($join) {
                $join->on('product_details.id', '=', 'order_details.product_detail_id');
            })->select(DB::raw('sum(order_details.qty * product_details.weight) as totalweight'))->first()->totalweight;
    }
    /**
     * getCart
     *
     * @return array
     */
    public function getCart(): array
    {
        $hasCheckout = $this->hasCheckout();
        if ($hasCheckout)
            return $hasCheckout();

        $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']])->first()
            ?->load([
                'orderDetails:id,order_id,product_detail_id,qty',
                'orderDetails.productDetail:id,product_id,gender,age,size,color,fabric,model,price,weight',
                'orderDetails.productDetail.product:id,product_brand_id,name',
                'orderDetails.productDetail.product.productImage:id,product_images.product_id,image',
                'orderDetails.productDetail.product.productBrand:id,name,image'
            ])->get();
        return array(
            'code' => $cart?->all() ? 200 : 404,
            'data' => $cart?->all()
        );
    }
    /**
     * addQty
     *
     * @param  int $orderDetailId
     * @return array
     */
    public function addQty(int $orderDetailId): array
    {
        $hasCheckout = $this->hasCheckout();
        if ($hasCheckout)
            return $hasCheckout();

        $orderDetail = Order::where([['user_id', auth()->user()->id], ['status', 'cart']])
            ->first()->orderDetails()->find($orderDetailId);
        if (!$orderDetail)
            return array(
                'code' => 404,
                'data' => array()
            );
        $productDetail = $orderDetail->productDetail()->first();
        if ($orderDetail->qty < $productDetail->stock) {
            $orderDetail->qty += 1;
            if ($orderDetail->save())
                return array(
                    'code' => 200,
                    'data' => array(
                        'qty' => $orderDetail->qty
                    )
                );
            else
                return array(
                    'code' => 500
                );
        } else {
            return array(
                'code' => 400,
                'redirect' => array(
                    'url' => back()->getTargetUrl()
                ),
                'errors' => array(
                    'qty' => 'Jumlah pesanan tidak boleh melebihi stock'
                )
            );
        }
    }
    /**
     * subQty
     *
     * @param  int $orderDetailId
     * @return array
     */
    public function subQty(int $orderDetailId): array
    {
        $hasCheckout = $this->hasCheckout();
        if ($hasCheckout)
            return $hasCheckout();

        $orderDetail = Order::where([['user_id', auth()->user()->id], ['status', 'cart']])
            ->first()->orderDetails()->find($orderDetailId);
        if (!$orderDetail)
            return array(
                'code' => 404,
                'data' => array()
            );
        if ($orderDetail->qty > 1) {
            $orderDetail->qty -= 1;
            if ($orderDetail->save())
                return array(
                    'code' => 200,
                    'data' => array(
                        'qty' => $orderDetail->qty
                    )
                );
            else
                return array(
                    'code' => 500
                );
        } else {
            return array(
                'code' => 400,
                'redirect' => array(
                    'url' => back()->getTargetUrl()
                ),
                'errors' => array(
                    'qty' => 'Jumlah pesanan tidak boleh kurang dari 1'
                )
            );
        }
    }
    /**
     * removeItem
     *
     * @param  int $orderDetailId
     * @return array
     */
    public function removeItem(int $orderDetailId): array
    {
        $hasCheckout = $this->hasCheckout();
        if ($hasCheckout)
            return $hasCheckout();

        $orderDetail = Order::where([['user_id', auth()->user()->id], ['status', 'cart']])
            ->first()->orderDetails()->find($orderDetailId);
        if (!$orderDetail)
            return array(
                'code' => 404,
                'data' => array()
            );
        if ($orderDetail->delete())
            return array(
                'code' => 200,
                'data' => array(
                    'message' => 'item berhasil dihapus'
                )
            );
        else
            return array(
                'code' => 500
            );
    }
    /**
     * addToCart
     *
     * @param  Product $product
     * @param  int $productDetailId
     * @param  int $qty
     * @return array
     */
    public function addToCart(Product $product, int $productDetailId, int $qty): array
    {
        $hasCheckout = $this->hasCheckout();
        if ($hasCheckout)
            return $hasCheckout();

        $productDetail = $product->productDetails()->find($productDetailId);
        if (!$productDetail) {
            return array(
                'code' => 404,
                'data' => array()
            );
        }
        if ($productDetail->stock < $qty || 0 >= $qty) {
            return array(
                'code' => 400,
                'redirect' => array(
                    'url' => back()->getTargetUrl()
                ),
                'errors' => array(
                    'qty' => 'Jumlah pesanan tidak boleh melebihi stock atau kurang dari 1'
                )
            );
        }
        $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']]);
        if (!$cart->exists())
            $cart = Order::create([
                'user_id' => auth()->user()->id,
                'code' => str()->orderedUUid(),
                'subtotal' => 0,
                'totalweight' => 0,
                'status' => 'cart'
            ])->first();
        $cart->first()->orderDetails()->create([
            'product_detail_id' => $productDetail->id,
            'qty' => $qty
        ]);
        return array(
            'code' => 200,
            'data' => array(
                'message' => 'Produk berhasil ditambahkan ke keranjang'
            )
        );
    }
    /**
     * checkoutCart
     *
     * @return array
     */
    public function checkoutCart(): array
    {
        $hasCheckout = $this->hasCheckout();
        if ($hasCheckout)
            return $hasCheckout();
        $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']]);
        $productSoldOut = $this->checkUnvailableProducts($cart);
        if ($productSoldOut)
        return array(
            'code' => 400,
            'redirect' => array(
                'url' => back()->getTargetUrl()
            ),
            'errors' => $productSoldOut
        );
        $addresses = ShippingAddress::where('user_id', auth()->user()->id);
        $cart->first()->shipping()->create(
            ['shipping_address_id' => $addresses->clone()->selected()->first()?->id ?? $addresses->clone()->primary()->first()?->id]
        );
        $isUpdated = $cart->update([
            'totalweight' => $this->calcWeightTotal($cart),
            'subtotal' => $this->calcSubTotal($cart),
            'status' => 'checkout'
        ]);
        if ($isUpdated) {
            return array(
                'code' => 200,
                'data' => array(
                    'url' => url('customer/checkout'),
                    'message' => 'Keranjang berhasil di-checkout'
                )
            );
        } else {
            return array(
                'code' => 500
            );
        }
    }
}
