<?php
namespace App\Services\Product;

use App\Models\Product;

interface ProductService 
{
    public function getProducts(int $offset = null, int $limit = null, int $paginate = null) : array;
    public function getProductsByAvailability(string $availability, int $offset = null, int $limit = null, int $paginate = null) : array;
    public function getProductsPromo(int $offset = null, int $limit = null, int $paginate = null) : array;
    public function getProductsByBrand(array $brandIds, int $offset = null, int $limit = null, int $paginate = null) : array;
    public function getProduct(Product $product) : array;
    public function searchProducts($criteria, int $offset = null, int $limit = null) : array;
    public function searchProductsPromo($criteria, int $paginate = null) : array;
    public function searchProductsByAvailability($criteria, string $availability, int $paginate = null) : array;
    public function searchProductsByBrand($criteria, array $brandIds, int $paginate = null) : array;
}