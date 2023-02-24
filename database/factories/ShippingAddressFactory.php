<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingAddress>
 */
class ShippingAddressFactory extends Factory
{
    private function getAllProvices()
    {
        $path = storage_path('json') . '/province.json';
        $provinces = json_decode(file_get_contents($path, true))->rajaongkir->results;
        $provinces = collect($provinces);
        return $provinces->pluck('province_id');
    }
    private function getAllCities($provinceId)
    {
        $path = storage_path('json') . '/city.json';
        $cities = json_decode(file_get_contents($path, true))->rajaongkir->results;
        $cities = collect($cities);
        return $cities->where('province_id', $provinceId)->pluck('city_id');
    }
    private function getAllSubdistrict($provinceId, $cityId)
    {
        $path = storage_path('json') . '/subdistrict/'. (int) $cityId . '.json';
        if (file_exists($path)) {
            $subdistricts = json_decode(file_get_contents($path, true))->rajaongkir->results;
            $subdistricts = collect($subdistricts);
            return $subdistricts->where('province_id', $provinceId)->where('city_id', $cityId)->pluck('subdistrict_id');
        } 
        return collect([]);
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $provinceId = $this->getAllProvices()->random();
        $cityId = $this->getAllCities($provinceId)->random();
        $subdistrictId = $this->getAllSubdistrict($provinceId, $cityId)->random();
        return [
            'addressname' => Str::random(8),
            'shippingname' => fake()->name(),
            'phonenumber' => fake()->phoneNumber(),
            'province_id' => $provinceId,
            'city_id' => $cityId,
            'subdistrict_id' => $subdistrictId,
            'zip' => '62257',
            'fulladdress' => fake()->address(),
            'isselect' => false,
            'type' => 'secondary'
        ];
    }
}
