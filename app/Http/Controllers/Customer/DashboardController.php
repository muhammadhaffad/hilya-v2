<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function index() 
    {
        $result = $this->orderService->getCountOrder();
        return view('v2.customer.dashboard.index', ['datas' => $result['data']]);
    }
}
