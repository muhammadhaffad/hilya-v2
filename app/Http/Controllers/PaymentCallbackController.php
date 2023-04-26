<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
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
                    $payment = Payment::where('order_code', $attr['order_id']);
                    $payment->update([
                        'status' => 'paid',
                        'settlementtime' => $attr['settlement_time']
                    ]);
                    $payment->first()->order()->update([
                        'status' => 'paid'
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
                    $payment = Payment::where('order_code', $attr['order_id']);
                    $payment->update([
                        'status' => $transactionStatus
                    ]);
                    $payment->first()->order()->update([
                        'status' => $transactionStatus
                    ]);
                    $orderItems = $payment->first()->order()->first()->load('orderItems.productItem.productOrigins')->orderItems;
                    foreach ($orderItems as $orderItem ) {
                        if ($orderItem->productItem->is_bundle) {
                            foreach ($orderItem->productItem->productOrigins as $productOrigin) {
                                $productOrigin->increment('stock', $orderItem->qty);
                            }
                        } 
                    }
                    foreach ($orderItems as $orderItem ) {
                        if ($orderItem->productItem->is_bundle) {
                            $orderItem->productItem->update([
                                'stock' => $orderItem->productItem->productOrigins->min('stock')
                            ]);
                        } else {
                            $orderItem->productItem->increment('stock', $orderItem->qty);
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
