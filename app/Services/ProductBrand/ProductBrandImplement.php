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
        return array(
            'code' => $productBrand->all() ? 200 : 404,
            'data' => $productBrand->all()
        );
    }
}