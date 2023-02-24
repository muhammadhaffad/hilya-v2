<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OrderHistoryPage\OrderItem;
use App\Http\Controllers\OrderHistoryPage\OrderHistory;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function orderHistory(Request $request)
    {
        auth()->loginUsingId(2);
        if ($request->get('search') || $request->get('status') || ($request->get('start_date') && $request->get('end_date')))
        {
            $orders = $this->orderService->searchOrders(auth()->user()->id, $request);
            return response(200)->json($orders);
        }
        else
        {
            $orders = $this->orderService->getOrders(auth()->user()->id);
            $orders['data'] ?: abort(404);
            return response()->json($orders);
        }
    }

    public function orderItem($code)
    {
        $order = $this->orderService->getDetailOrder(auth()->user()->id, $code);
        $order['data'] ?: abort(404);
        return response()->json($order);
    }
}
