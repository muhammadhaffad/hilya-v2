<?php

namespace App\Services\Cart;

use App\Models\ProductItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function PHPSTORM_META\map;

class CartServiceImplement implements CartService
{
    /**
     * hasCheckout
     *
     * @return mixed
     */
    public function hasCheckout(): mixed
    {
        if (Order::where([['user_id', auth()->user()->id], ['status', 'checkout']])->exists())
            return [
                'code' => 302,
                'message' => 'Selesaikan checkout terlebih dahulu'
            ];
        return false;
    }
    /**
     * checkUnvailableProducts
     *
     * @param  Builder $q
     * @return array
     */
    public function checkUnvailableProducts(): mixed
    {
        DB::beginTransaction();
        $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']])->first()->load([
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
        foreach ($cart->orderItems as $orderItem ) {
            if ($orderItem->productItem->is_bundle) {
                foreach ($orderItem->productItem->productOrigins as $productOrigin) {
                    $productOrigin->decrement('stock', $orderItem->qty);
                }
            } 
        }
        foreach ($cart->orderItems as $orderItem ) {
            foreach ($orderItem->product->productItems as $productItem) {
                if ($productItem->is_bundle) {
                    $productItem->update([
                        'stock' => $productItem->productOrigins()->min('stock')
                    ]);
                    if ($productItem->stock < 0) {
                        $unavailableProducts['cart.' . $productItem->id] = ['Produk item kosong atau jumlah pesanan melebihi stok'];
                    }
                }
            }
            if (!$orderItem->productItem->is_bundle) {
                $orderItem->productItem->decrement('stock', $orderItem->qty);
                if ($productItem->stock < 0) {
                    $unavailableProducts['cart.' . $productItem->id] = ['Produk item kosong atau jumlah pesanan melebihi stok'];
                }
            }
        }
        DB::rollBack();
        if ($unavailableProducts)
            return [
                'code' => 422,
                'message' => 'Cart item tidak valid',
                'errors' => $unavailableProducts
            ];
        else
            return false;
        /* $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']]);
        $cartItems = $cart->join('order_items', 'order_id', 'orders.id')
            ->join('product_items', function ($join) {
                $join->on('product_items.id', '=', 'order_items.product_item_id')
                    ->on('product_items.stock', '<', 'order_items.qty');
            })->get()
            ->mapWithKeys(fn ($item) => ['cart.' . $item->id => ['Produk item kosong atau jumlah pesanan melebihi stok']])
            ->toArray();
        if ($cartItems)
            return [
                'code' => 422,
                'message' => 'Cart item tidak valid',
                'errors' => $cartItems
            ];
        else
            return false; */
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
        return $q->join('order_items', 'order_id', 'orders.id')
            ->join('product_items', function ($join) {
                $join->on('product_items.id', '=', 'order_items.product_item_id');
            })->select(DB::raw('sum(order_items.qty * product_items.price) as subtotal'))->first()->subtotal;
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
        return $q->join('order_items', 'order_id', 'orders.id')
            ->join('product_items', function ($join) {
                $join->on('product_items.id', '=', 'order_items.product_item_id');
            })->select(DB::raw('sum(order_items.qty * product_items.weight) as totalweight'))->first()->totalweight;
    }
    /**
     * getCart
     *
     * @return mixed
     */
    public function getCart(): mixed
    {
        $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']])->first()
            ?->load([
                'orderItems:id,order_id,product_item_id,qty',
                'orderItems.productItem:id,product_id,gender,age,size,color,price,weight,note_bene,stock,is_bundle,discount',
                'orderItems.productItem.product:id,product_brand_id,name,availability,ispromo',
                'orderItems.productItem.product.productImage:id,product_images.product_id,image',
                'orderItems.productItem.product.productBrand:id,name,image'
            ]);
        if ($cart) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan cart items',
                'data' => $cart
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
    }
    /**
     * addQty
     *
     * @param  int $orderItemId
     * @return mixed
     */
    public function addQty(int $orderItemId): mixed
    {
        DB::beginTransaction();
        try {
            $orderItem = Order::where([['user_id', auth()->user()->id], ['status', 'cart']])
                ->first()->orderItems()->find($orderItemId);
            if (!$orderItem) {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
            $productItem = $orderItem->productItem()->first();
            if ($orderItem->qty < $productItem->stock) {
                $orderItem->qty += 1;
                $orderItem->save();
                DB::commit();
                return [
                    'code' => 204,
                    'message' => 'Sukses menambahkan jumlah pesanan'
                ];
            } else {
                return [
                    'code' => 422,
                    'message' => 'Data yang diberikan tidak valid',
                    'errors' => [
                        'qty' => ['Jumlah pesanan tidak boleh melebihi stock']
                    ]
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * subQty
     *
     * @param  int $orderItemId
     * @return array
     */
    public function subQty(int $orderItemId): mixed
    {
        DB::beginTransaction();
        try {
            $orderItem = Order::where([['user_id', auth()->user()->id], ['status', 'cart']])
                ->first()->orderItems()->find($orderItemId);
            if (!$orderItem) {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
            if ($orderItem->qty > 1) {
                $orderItem->qty -= 1;
                $orderItem->save();
                DB::commit();
                return [
                    'code' => 204,
                    'message' => 'Sukses mengurangi jumlah pesanan'
                ];
            } else {
                return [
                    'code' => 422,
                    'message' => 'Data yang diberikan tidak valid',
                    'errors' => [
                        'qty' => ['Jumlah pesanan tidak boleh kurang dari 1']
                    ]
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * removeItem
     *
     * @param  int $orderItemId
     * @return array
     */
    public function removeItem(int $orderItemId): mixed
    {
        DB::beginTransaction();
        try {
            $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']]);
            $orderItem = $cart->first()->orderItems()->find($orderItemId);
            if (!$orderItem)
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            $orderItem->delete();
            if ($cart->first()->orderItems()->count() == 0) {
                $cart->delete();
            }
            DB::commit();
            return [
                'code' => 204,
                'message' => 'Sukses menghapus item'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * addToCart
     *
     * @param  Product $product
     * @param  int $productItemId
     * @param  int $qty
     * @return array
     */
    public function addToCart(Product $product, array $attr): array
    {
        $validator = Validator::make($attr, [
            'product_item_id' => [
                'required',
                'integer',
                Rule::exists('product_items', 'id')->where(function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                }),
                Rule::notIn(Order::where([['user_id', 1], ['status', 'cart']])->first()?->orderItems()->get()->pluck('product_item_id')->toArray())
            ],
            'qty' => [
                'required',
                'integer',
                'min:1',
                Rule::when(ProductItem::where(['product_id'=>$product->id, 'id'=>@$attr['product_item_id']])->exists(), [
                    'max:'.ProductItem::find(@$attr['product_item_id'])?->stock
                ])
            ]
        ], [
            'required' => 'Data :attribute wajib diisi.',
            'integer' => 'Data :attribute wajib berupa angka.',
            'exists' => 'Data product item tidak ada',
            'min' => 'Data :attribute harus bernilai minimal :min.',
            'max' => 'Data :attribute harus bernilai maksimal :max.',
            'not_in' => 'Product item sudah berada di keranjang'
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
            $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']])->first();
            if (!$cart)
                $cart = Order::create([
                    'user_id' => auth()->user()->id,
                    'subtotal' => 0,
                    'totalweight' => 0,
                    'status' => 'cart'
                ]);
            $cart->orderItems()->create([
                'product_item_id' => $attr['product_item_id'],
                'qty' => $attr['qty']
            ]);
            DB::commit();
            return [
                'code' => 204,
                'message' => 'Produk berhasil ditambah ke keranjang'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * checkoutCart
     *
     * @return array
     */
    public function checkoutCart(): array
    {
        DB::beginTransaction();
        try {
            $cart = Order::where([['user_id', auth()->user()->id], ['status', 'cart']]);
            $addresses = ShippingAddress::where('user_id', auth()->user()->id);
            $cart->first()->shipping()->updateOrCreate(
                ['shipping_address_id' => $addresses->clone()->selected()->first()?->id ?? $addresses->clone()->primary()->first()?->id]
            );
            $cart->update([
                'totalweight' => $this->calcWeightTotal($cart),
                'subtotal' => $this->calcSubTotal($cart),
                'status' => 'checkout'
            ]);
            DB::commit();
            return [
                'code' => 204,
                'message' => 'Keranjang berhasil di-checkout'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
