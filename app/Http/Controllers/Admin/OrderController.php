<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    protected $orderService;
    
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $result = $this->orderService->searchOrders(0, $request->all());
        if ($result['code'] == 200) {
            // return $result;
            return view('v2.admin.order.index', ['orders' => $result['data']]);
        } else {
            return abort(404);
        }
    }

    public function dataOrder(Request $request)
    {
        $query = Order::withoutStatus(['cart', 'checkout'])
        ->with([
            'payment',
            'user'
        ])
        ->latest();
        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('payment.settlementtime', function($data) {
                return $data->payment->settlementtime ?: '-';
            })
            ->editColumn('grandtotal', function($data) {
                return \Helper::rupiah($data->grandtotal);
            })
            ->addColumn('payment.expiredtime', function($data) {
                return \Carbon\Carbon::parse($data->payment->transactiontime)->addHours(24)->format('Y-m-d H:i:s');
            })
            ->addColumn('action', function($data) {
                return view('v2.admin.order.datatables.action', compact('data'));
            })
            ->removeColumn('user.password')
            ->make(true);
    }

    public function show($code)
    {
        $result = $this->orderService->getDetailOrder(0, $code);
        if ($result['code'] == 200) {
            return view('v2.admin.order.show', ['order' => $result['data']]);
        } else {
            abort(404);
        }
    }

    public function setProcessing($code) {
        $result = $this->orderService->setProcessing($code);
        if ($result['code'] == 204) {
            return redirect()->back()->with('message',$result['message']);
        } else {
            abort(404);
        }
    }

    public function setShipping(Request $request, $code) {
        $result = $this->orderService->setShipping($code, $request->all());
        if ($result['code'] == 204) {
            return redirect()->back()->with('message',$result['message']);
        } else {
            abort(404);
        }
    }

    public function setSuccess($code) {
        $result = $this->orderService->setSuccess($code);
        if ($result['code'] == 204) {
            return redirect()->back()->with('message',$result['message']);
        } else {
            abort(404);
        }
    }
}
