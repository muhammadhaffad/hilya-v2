<?php

namespace App\Helpers;

use App\Models\ProductBrand;

class Helper
{
    public static function rupiah($number)
    {
        return 'Rp'.number_format($number,0,',','.');
    }

    public static function getProvinces()
    {
        $path = storage_path('json') . '/province.json';
        $provinces = json_decode(file_get_contents($path, true))->rajaongkir->results;
        $provinces = collect($provinces);
        return $provinces;
    }

    public static function getProvince($provinces, $province_id)
    {
        return $provinces->where('province_id', $province_id)->pluck('province')->first();
    }

    public static function getCities()
    {
        $path = storage_path('json') . '/city.json';
        $cities = json_decode(file_get_contents($path, true))->rajaongkir->results;
        $cities = collect($cities);
        return $cities;
    }

    public static function getCity($cities, $city_id)
    {
        return $cities->where('city_id', $city_id)->pluck('city_name')->first();
    }

    public static function getSubdistricts($city_ids)
    {
        $subdistricts = collect([]);
        foreach ($city_ids as $city_id) {
            $path = storage_path('json') . '/subdistrict/'. (int) $city_id . '.json';
            $cities = json_decode(file_get_contents($path, true))->rajaongkir->results;
            $cities = collect($cities);
            $subdistricts = $subdistricts->concat($cities);
        }
        return $subdistricts;
    }

    public static function getSubdistrict($subdistricts, $subdistrict_id)
    {
        return $subdistricts->where('subdistrict_id', $subdistrict_id)->pluck('subdistrict_name')->first();
    }

    public static function getBrands() 
    {
        return ProductBrand::all();
    }
}
