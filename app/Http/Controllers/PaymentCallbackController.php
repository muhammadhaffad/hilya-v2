<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductOrigin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentCallbackController extends Controller
{
    public function callback(Request $request) 
    {
        $attr = $request->all();
        $serverKey = env('MIDTRANS_SERVER_KEY_B64_DECODE');
        $signature = hash('sha512', $attr['order_id'].$attr['status_code'].$attr['gross_amount'].$serverKey);
        if ($attr['signature_key'] === $signature) {
            if ($attr['transaction_status'] === 'settlement' || $attr['transaction_status'] === 'capture') {
                DB::beginTransaction();
                try {
                    $order = Order::where('code', $attr['order_id']);
                    $order->update([
                        'status' => 'paid'
                    ]);
                    $order->first()->payment()->update([
                        'status' => 'paid',
                        'settlementtime' => $attr['settlement_time']
                    ]);
                    DB::commit();
                    return response('', 200);
                } catch (Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            } else {
                DB::beginTransaction();
                try {
                    $transactionStatus = '';
                    switch ($attr['transaction_status']) {
                        case 'deny':
                            $transactionStatus = 'denied';
                            break;
                        case 'cancel':
                            $transactionStatus = 'canceled';
                            break;
                        case 'expire':
                            $transactionStatus = 'expired';
                            break;
                        default:
                            $transactionStatus = $attr['transaction_status'];
                            break;
                    }
                    $order = Order::where('code', $attr['order_id']);
                    $order->clone()->update([
                        'status' => $transactionStatus
                    ]);
                    $order->first()->payment()->update([
                        'status' => $transactionStatus
                    ]);
                    $orderItems = $order->first()->load('orderItems.productItem.productOrigins')->orderItems;
                    foreach ($orderItems as $orderItem ) {
                        if ($orderItem->productItem->is_bundle) {
                            $productOriginIds = $orderItem->productItem->productOrigins->pluck('id');
                            ProductOrigin::whereIn('id', $productOriginIds)->update([
                                'stock' => \DB::raw('stock + ' . $orderItem->qty)
                            ]);
                        } 
                    }
                    $orderItems = $order->first()->load('orderItems.productItem.productOrigins')->orderItems;
                    foreach ($orderItems as $orderItem ) {
                        if ($orderItem->productItem->is_bundle) {
                            $orderItem->productItem->update([
                                'stock' => $orderItem->productItem->productOrigins->min('stock')
                            ]);
                        } else {
                            $orderItem->productItem->update([
                                'stock' => \DB::raw('stock + ' . $orderItem->qty) 
                            ]);
                        }
                    }
                    DB::commit();
                    return response('', 200);
                } catch (Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        } else {
            return abort(403);
        }
    }
}
