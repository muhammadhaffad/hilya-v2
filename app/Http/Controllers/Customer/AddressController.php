<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Address\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected $addressService;
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {
            $result = $this->addressService->createAddress($request->all());
            if ($result['code'] == 422) {
                return redirect()->back()->withErrors($result['errors'])->withInput();
            } else {
                return redirect()->back()->with('message', 'Alamat berhasil dibuat');
            }
        } else {
            return view('v2.customer.address-book.create');
        }
    }

    public function index()
    {
        $result = $this->addressService->getAddresses();
        if ($result['code'] == 200) {
            return view('v2.customer.address-book.index', ['addresses' => $result['data']]);
        }
        abort(404);
    }

    public function edit($addressId)
    {
        $result = $this->addressService->getAddress($addressId);
        if ($result['code'] == 200) {
            return view('v2.customer.address-book.edit', ['address' => $result['data']]);
        }
        abort(404);
    }

    public function update(Request $request, $addressId) 
    {
        $result = $this->addressService->updateAddress($addressId, $request->all());
        if ($result['code'] == 204) {
            return redirect()->back()->with('message', 'Alamat berhasil diperbarui');
        } elseif ($result['code'] == 422) {
            return redirect()->back()->withErrors($result['errors']);
        } else {
            return abord(500);
        }
    }

    public function destroy($addressId)
    {
        $result = $this->addressService->removeAddress($addressId);
        return redirect()->back()->with('message', $result['message']);
    }

    public function select($addressId)
    {
        $result = $this->addressService->selectAddress($addressId);
        return redirect()->back()->with('message', $result['message']);
    }

    public function provinces()
    {
        $path = storage_path('json') . '/province.json';
        $provinces = json_decode(file_get_contents($path, true))->rajaongkir->results;
        $provinces = collect($provinces);
        return $provinces->mapWithKeys(fn($item) => [$item->province_id => $item->province])->toJson();
    }

    public function cities($provinceId)
    {
        $path = storage_path('json') . '/city.json';
        $cities = json_decode(file_get_contents($path, true))->rajaongkir->results;
        $cities = collect($cities);
        return $cities->where('province_id', $provinceId)
            ->mapWithKeys(fn($item) => [$item->city_id => $item->city_name])->toJson();
    }

    public function subdistricts($provinceId, $cityId)
    {
        $path = storage_path('json') . '/subdistrict/'. (int) $cityId . '.json';
        if (file_exists($path)) {
            $subdistricts = json_decode(file_get_contents($path, true))->rajaongkir->results;
            $subdistricts = collect($subdistricts);
            return $subdistricts->where('province_id', $provinceId)->where('city_id', $cityId)
                ->mapWithKeys(fn($item) => [$item->subdistrict_id => $item->subdistrict_name])->toJson();
        } 
        return [];
    }
}
