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
    public function index(Request $request)
    {
        $result = $this->orderService->searchOrders(auth()->user()->id, $request->all());
        if ($result['code'] == 200) {
            // return $result;
            return view('v2.order.index', ['orders' => $result['data']]);
        } else {
            return abort(404);
        }
    }
    public function show($code)
    {
        $result = $this->orderService->getDetailOrder(auth()->user()->id, $code);
        if ($result['code'] == 200) {
            // return $result['data'];
            return view('v2.order.show', ['order' => $result['data']]);
        } else {
            return abort(404);
        }
    }
}
