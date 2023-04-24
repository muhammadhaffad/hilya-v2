<?php
namespace App\Services\ProductBrand;

interface ProductBrandService
{
    public function getAllBrand() : array;
    public function updateBrand($id, $attr) : array;
    public function storeBrand($attr) : array;
    public function deleteBrand($id) : array;
}