<?php

namespace App\Services\Address;

use App\Helpers\Helper;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddressServiceImplement implements AddressService
{
    public function getAddresses(): array
    {
        $addresses = ShippingAddress::where('user_id', auth()->user()->id)->orderBy('isselect', 'desc')->get();
        if (!$addresses->isEmpty()) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan data alamat',
                'data' => $addresses
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data',
            ];
        }      
    }

    public function getAddress($addressId): array
    {
        $address = ShippingAddress::where([['user_id', auth()->user()->id], ['id', $addressId]])->first();
        if ($address) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan data alamat',
                'data' => $address
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data',
            ];
        }
    }

    public function createAddress($attr): array
    {
        $validator = Validator::make($attr, [
            'addressname' => 'bail|required|string',
            'shippingname' => 'bail|required|string',
            'phonenumber' => 'bail|required|numeric',
            'province_id' => 'bail|required|numeric',
            'city_id' => 'bail|required|numeric',
            'subdistrict_id' => 'bail|required|numeric',
            'zip' => 'bail|required|numeric',
            'fulladdress' => 'required'
        ], [
            'required' => 'Data :attribute wajib diisi',
            'string' => 'Data :attribute wajib berupa teks',
            'numeric' => 'Data :attribute wajib berupa angka'
        ], [
            'addressname' => 'nama alamat',
            'shippingname' => 'nama penerima',
            'phonenumber' => 'nomor telepon',
            'province_id' => 'id provinsi',
            'city_id' => 'id kota/kabupaten',
            'subdistrict_id' => 'id kecamatan',
            'zip' => 'kode pos',
            'fulladdress' => 'alamat lengkap'
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
            $subdistricts = Helper::getSubdistricts([$attr['city_id']]);
            $subdistricts = $subdistricts->where('province_id', $attr['province_id'])->where('subdistrict_id', $attr['subdistrict_id'])->first();
            if (!$subdistricts) {
                return [
                    'code' => 422,
                    'message' => 'Data yang diberikan tidak valid',
                    'errors' => [
                        'address' => ['Provinsi/Kabupaten/Kota/Kecamatan tidak sesuai']
                    ]
                ];
            }
            $address = ShippingAddress::create([
                'user_id' => auth()->user()->id,
                'addressname' => $attr['addressname'],
                'shippingname' => $attr['shippingname'],
                'phonenumber' => $attr['phonenumber'],
                'province_id' => $attr['province_id'],
                'city_id' => $attr['city_id'],
                'subdistrict_id' => $attr['subdistrict_id'],
                'zip' => $attr['zip'],
                'fulladdress' => $attr['fulladdress'],
                'isselect' => false,
                'type' => 'secondary'
            ]);
            DB::commit();
            return [
                'code' => 201,
                'message' => 'Alamat berhasil dibuat',
                'data' => $address
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateAddress($addressId, $attr): array
    {
        $validator = Validator::make($attr, [
            'addressname' => 'bail|required|string',
            'shippingname' => 'bail|required|string',
            'phonenumber' => 'bail|required|numeric',
            'province_id' => 'bail|required|numeric',
            'city_id' => 'bail|required|numeric',
            'subdistrict_id' => 'bail|required|numeric',
            'zip' => 'bail|required|numeric',
            'fulladdress' => 'required'
        ], [
            'required' => 'Data :attribute wajib diisi',
            'string' => 'Data :attribute wajib berupa teks',
            'numeric' => 'Data :attribute wajib berupa angka'
        ], [
            'addressname' => 'nama alamat',
            'shippingname' => 'nama penerima',
            'phonenumber' => 'nomor telepon',
            'province_id' => 'id provinsi',
            'city_id' => 'id kota/kabupaten',
            'subdistrict_id' => 'id kecamatan',
            'zip' => 'kode pos',
            'fulladdress' => 'alamat lengkap'
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
            $subdistricts = Helper::getSubdistricts([$attr['city_id']]);
            $subdistricts = $subdistricts->where('province_id', $attr['province_id'])->where('subdistrict_id', $attr['subdistrict_id'])->first();
            if (!$subdistricts) {
                return [
                    'code' => 422,
                    'message' => 'Data yang diberikan tidak valid',
                    'errors' => [
                        'address' => ['Provinsi/Kabupaten/Kota/Kecamatan tidak sesuai']
                    ]
                ];
            }
            $address = ShippingAddress::where([['id', $addressId],['user_id', auth()->user()->id]]);
            if ($address->count() == 0) {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
            $address->update([
                'addressname' => $attr['addressname'],
                'shippingname' => $attr['shippingname'],
                'phonenumber' => $attr['phonenumber'],
                'province_id' => $attr['province_id'],
                'city_id' => $attr['city_id'],
                'subdistrict_id' => $attr['subdistrict_id'],
                'zip' => $attr['zip'],
                'fulladdress' => $attr['fulladdress']
            ]);
            DB::commit();
            return [
                'code' => 204,
                'message' => 'Alamat berhasil diubah'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function removeAddress($addressId): array
    {
        try {
            $address = ShippingAddress::where([['id', $addressId],['user_id', auth()->user()->id]]);
            if ($address->count() == 0) {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
            if ($address->first()->type == 'primary')
            {
                return [
                    'code' => 500,
                    'message' => 'Gagal menghapus data'
                ];
            }
            $address->delete();
            return [
                'code' => 204,
                'message' => 'Data berhasil dihapus'
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            return [
                'code' => 500,
                'message' => 'Gagal menghapus data, alamat sedang digunakan.'
            ];
        }
    }

    public function selectAddress($addressId): array
    {
        $address = ShippingAddress::where([['id', $addressId],['user_id', auth()->user()->id]]);
        if ($address->count() == 0) {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
        ShippingAddress::where('user_id', auth()->user()->id)->update([
            'isselect' => false
        ]);
        $address->update([
            'isselect' => true
        ]);
        return [
            'code' => 204,
            'message' => 'Alamat berhasil dipilih'
        ];
    }
}
