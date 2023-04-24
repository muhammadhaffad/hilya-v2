<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        return view('v2.admin.product.index');
    }

    public function create(Request $request)
    {
        return view('v2.admin.product.create');
    }

    public function store(Request $request)
    {
        $result = $this->productService->createProduct($request->all());
        if ($result['code'] == 204) {
            return redirect()->back()->with('message', $result['message']);
        } elseif ($result['code'] == 422) {
            return redirect()->back()->withErrors($result['errors'])->withInput();
        }
    }

    public function edit($id)
    {
        $result = $this->productService->showProduct($id);
        if ($result['code'] == 200) {
            return view('v2.admin.product.edit', ['product' => $result['data']]);
        } else {
            return abort(404);
        }
    }

    public function update(Request $request, $id) 
    {
        $result = $this->productService->updateProduct($id, $request->all());
        if ($result['code'] == 204) {
            return back()->with('message', $result['message']);
        }
        return $result;
    }

    public function destroy($id)
    {
        $result = $this->productService->deleteProduct($id);
        if ($result['code'] == 204) {
            return redirect()->back()->with('message', $result['message']);
        }
        if ($result['code'] == 404) {
            return abort(404);
        }
    }

    public function dataProduct(Request $request)
    {
        $query = Product::with(['productBrand', 'productImage'])->latest();
        if ($request->get('brand')) {
            $query->where('product_brand_id', $request->get('brand'));
        }
        if ($request->get('status')) {
            $query->where('availability', $request->get('status'));
        }
        if ($request->get('ispromo')) {
            $query->where('ispromo', $request->get('ispromo') == 'true' ? true : false);
        }
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('productImage.image', function ($data) {
                return view('v2.admin.product.datatables.image', compact('data'));
            })
            ->editColumn('productBrand.name', function ($data) {
                return $data->productBrand->name;
            })
            ->editColumn('availability', function ($data) {
                return \Str::title($data->availability);
            })
            ->editColumn('ispromo', function ($data) {
                return $data->ispromo ? 'Iya' : 'Tidak';
            })
            ->addColumn('action', function ($data) {
                return view('v2.admin.product.datatables.action', compact('data'));
            })
            ->make(true);
    }
}
