<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductBrand;
use App\Services\ProductBrand\ProductBrandService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BrandController extends Controller
{
    protected $productBrandService;

    public function __construct(ProductBrandService $productBrandService)
    {
        $this->productBrandService = $productBrandService;
    }

    public function index()
    {
        return view('v2.admin.brand.index');
    }

    public function create()
    {
        return view('v2.admin.brand.create');
    }

    public function store(Request $request)
    {
        $result = $this->productBrandService->storeBrand($request->all());
        if ($result['code'] == 204) {
            return redirect()->back()->with('message', $result['message']);
        } 
    }

    public function edit($id)
    {
        $brand = ProductBrand::find($id);
        return view('v2.admin.brand.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $result = $this->productBrandService->updateBrand($id, $request->all());
        if ($result['code'] == 204) {
            return redirect()->back()->with('message', $result['message']);
        }
    }
    
    public function destroy($id)
    {
        $result = $this->productBrandService->deleteBrand($id);
        if ($result['code'] == 204) {
            return redirect()->back()->with('message', $result['message']);
        }
    }

    public function dataBrand()
    {
        $query = ProductBrand::all();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('image', function ($data) {
                return view('v2.admin.brand.datatables.image', compact('data'));
            })
            ->addColumn('action', function ($data) {
                return view('v2.admin.brand.datatables.action', compact('data'));
            })
            ->make(true);
    }
}
