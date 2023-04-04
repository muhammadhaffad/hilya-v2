<?php
namespace App\Services\ProductBrand;

use App\Models\ProductBrand;

class ProductBrandImplement implements ProductBrandService
{    
    /**
     * getAllBrand
     *
     * @return array
     */
    public function getAllBrand() : array
    {
        $productBrand = ProductBrand::get();
        if (!$productBrand->isEmpty()) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan product brands',
                'data' => $productBrand
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data',
            ];
        }
    }
}