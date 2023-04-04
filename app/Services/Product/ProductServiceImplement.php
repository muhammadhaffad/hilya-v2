<?php

namespace App\Services\Product;

use App\Models\Product;

class ProductServiceImplement implements ProductService
{
    /**
     * columns 
     * 
     * Digunakan untuk pencarian produk
     * 
     * @var array
     */
    protected $columns = [
        'products.name',
        'products.description',
        'products.availability',
        'products.ispromo',
        'product_brands.name',
        'product_items.gender',
        'product_items.age',
        'product_items.color'
    ];

    /**
     * getProducts
     *
     * @param  int $offset
     * @param  int $limit
     * @param  int $paginate
     * @return array
     */
    public function getProducts(int $offset = null, int $limit = null, int $paginate = null): array
    {
        $products = Product::whereHas('productItems', fn ($q) => $q->inStock())
            ->with(['productImages:product_id,id,image', 'productBrand:id,name'])
            ->withMin('productItems', 'price')
            ->withMax('productItems', 'price')
            ->latest();
        if ($offset !== null && $limit !== null) {
            if ($limit < 0)
                $limit = 0;
            if ($limit > 40)
                $limit = 40;
            $productsLimit = $products->limit($limit)->get();
            if (!$productsLimit->isEmpty()) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsLimit
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        if ($paginate !== null) {
            $productsPagination = $products->cursorPaginate($paginate)->withQueryString();
            if (!$productsPagination) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsPagination
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        $products = $products->get();
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data products',
            'data' => $products
        ];
    }

    /**
     * getProductsByAvailability
     *
     * @param  string $availability
     * @param  int $offset
     * @param  int $limit
     * @param  int $paginate
     * @return array
     */
    public function getProductsByAvailability(string $availability, int $offset = null, int $limit = null, int $paginate = null): array
    {
        $products = Product::{$availability}()->whereHas('productItems', fn ($q) => $q->inStock())
            ->with(['productImages:product_id,id,image', 'productBrand:id,name'])
            ->withMin('productItems', 'price')
            ->withMax('productItems', 'price')
            ->latest();
        if ($offset !== null && $limit !== null) {
            if ($limit < 0)
                $limit = 0;
            if ($limit > 40)
                $limit = 40;
            $productsLimit = $products->limit($limit)->get();
            if (!$productsLimit->isEmpty()) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsLimit
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        if ($paginate !== null) {
            $productsPagination = $products->cursorPaginate($paginate)->withQueryString();
            if (!$productsPagination) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsPagination
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        $products = $products->get();
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data products',
            'data' => $products
        ];
    }

    /**
     * getProductsPromo
     *
     * @param  int $offset
     * @param  int $limit
     * @param  int $paginate
     * @return array
     */
    public function getProductsPromo(int $offset = null, int $limit = null, int $paginate = null): array
    {
        $products = Product::promo()->whereHas('productItems', fn ($q) => $q->inStock())
            ->with(['productImages:product_id,id,image', 'productBrand:id,name'])
            ->withMin('productItems', 'price')
            ->withMax('productItems', 'price')
            ->latest();
        if ($offset !== null && $limit !== null) {
            if ($limit < 0)
                $limit = 0;
            if ($limit > 40)
                $limit = 40;
            $productsLimit = $products->limit($limit)->get();
            if (!$productsLimit->isEmpty()) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsLimit
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        if ($paginate !== null) {
            $productsPagination = $products->cursorPaginate($paginate)->withQueryString();
            if (!$productsPagination) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsPagination
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        $products = $products->get();
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data products',
            'data' => $products
        ];
    }
    /**
     * getProductsByBrand
     *
     * @param  array $brandIds
     * @param  int $offset
     * @param  int $limit
     * @param  int $paginate
     * @return array
     */
    public function getProductsByBrand(array $brandIds, int $offset = null, int $limit = null, int $paginate = null): array
    {
        $products = Product::withBrand($brandIds)->whereHas('productItems', fn ($q) => $q->inStock())
            ->with(['productImages:product_id,id,image', 'productBrand:id,name'])
            ->withMin('productItems', 'price')
            ->withMax('productItems', 'price')
            ->latest();
        if ($offset !== null && $limit !== null) {
            if ($limit < 0)
                $limit = 0;
            if ($limit > 40)
                $limit = 40;
            $productsLimit = $products->limit($limit)->get();
            if (!$productsLimit->isEmpty()) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsLimit
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        if ($paginate !== null) {
            $productsPagination = $products->cursorPaginate($paginate)->withQueryString();
            if (!$productsPagination) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsPagination
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        $products = $products->get();
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data products',
            'data' => $products
        ];
    }

    /**
     * getProduct
     *
     * @param  Product $product
     * @return array
     */
    public function getProduct(Product $product): array
    {
        $product = $product?->load([
            'productBrand:id,name',
            'productImages:product_id,id,image',
            'productItems'
        ])->loadMin('productItems', 'price')->loadMax('productItems', 'price');
        if ($product) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan detail produk',
                'data' => $product
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
    }
    /**
     * searchProducts
     *
     * @param  mixed $criteria
     * @param  int $offset
     * @param  int $limit
     * @return array
     */
    public function searchProducts($criteria, int $offset = null, int $limit = null): array
    {
        $ignoredColumns = [''];
        $products = Product::join('product_brands', 'product_brands.id', 'products.product_brand_id')
            ->join('product_items', 'product_items.product_id', 'products.id')
            ->join('product_images', 'product_images.product_id', 'products.id')
            ->distinct()
            ->where(function ($query) use ($criteria, $ignoredColumns) {
                foreach ($this->columns as $key => $column) {
                    if (in_array($column, $ignoredColumns))
                        continue;
                    if ($column == 'products.ispromo') {
                        if ($criteria['q'] === 'promo')
                            $query->orWhere('ispromo', 1);
                        else
                            $query;
                    } else {
                        $query->orWhere($column, 'LIKE', '%' . $criteria['q'] . '%');
                    }
                }
            })
            ->where('product_items.stock', '>', 0)
            ->withMin('productItems', 'price')
            ->withMax('productItems', 'price')
            ->with(['productBrand:id,name', 'productImages:product_id,image'])
            ->latest();
        if ($offset !== null && $limit !== null) {
            if ($limit < 0)
                $limit = 0;
            if ($limit > 40)
                $limit = 40;
            $productsLimit = $products->limit($limit)->get();
            if (!$productsLimit->isEmpty()) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsLimit
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        $products = $products->get();
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data products',
            'data' => $products
        ];
    }
    /**
     * searchProductsPromo
     *
     * @param  mixed $criteria
     * @param  int $paginate
     * @return array
     */
    public function searchProductsPromo($criteria, int $paginate = null): array
    {
        $ignoredColumns = ['products.ispromo'];
        $products = Product::promo()->join('product_brands', 'product_brands.id', 'products.product_brand_id')
            ->join('product_items', 'product_items.product_id', 'products.id')
            ->join('product_images', 'product_images.product_id', 'products.id')
            ->distinct()
            ->where(function ($query) use ($criteria, $ignoredColumns) {
                foreach ($this->columns as $key => $column) {
                    if (in_array($column, $ignoredColumns))
                        continue;
                    $query->orWhere($column, 'LIKE', '%' . $criteria['q'] . '%');
                }
            })
            ->where('product_items.stock', '>', 0)
            ->withMin('productItems', 'price')
            ->withMax('productItems', 'price')
            ->with(['productBrand:id,name', 'productImages:product_id,image'])
            ->latest();
        if ($paginate !== null) {
            $productsPagination = $products->cursorPaginate($paginate)->withQueryString();
            if (!$productsPagination) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsPagination
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        $products = $products->get();
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data products',
            'data' => $products
        ];
    }
    /**
     * searchProductsByAvailability
     *
     * @param  mixed $criteria
     * @param  int $availability
     * @param  int $paginate
     * @return array
     */
    public function searchProductsByAvailability($criteria, string $availability, int $paginate = null): array
    {
        $ignoredColumns = ['products.availability'];
        $products = Product::{$availability}()->join('product_brands', 'product_brands.id', 'products.product_brand_id')
            ->join('product_items', 'product_items.product_id', 'products.id')
            ->join('product_images', 'product_images.product_id', 'products.id')
            ->distinct()
            ->where(function ($query) use ($criteria, $ignoredColumns) {
                foreach ($this->columns as $key => $column) {
                    if (in_array($column, $ignoredColumns))
                        continue;
                    if ($column == 'products.ispromo') {
                        if ($criteria['q'] === 'promo')
                            $query->orWhere('ispromo', 1);
                        else
                            $query;
                    } else {
                        $query->orWhere($column, 'LIKE', '%' . $criteria['q'] . '%');
                    }
                }
            })
            ->where('product_items.stock', '>', 0)
            ->withMin('productItems', 'price')
            ->withMax('productItems', 'price')
            ->with(['productBrand:id,name', 'productImages:product_id,image'])
            ->latest();
        if ($paginate !== null) {
            $productsPagination = $products->cursorPaginate($paginate)->withQueryString();
            if (!$productsPagination) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsPagination
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        $products = $products->get();
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data products',
            'data' => $products
        ];
    }
    /**
     * searchProductsByBrand
     *
     * @param  mixed $criteria
     * @param  int $brandId
     * @param  int $paginate
     * @return array
     */
    public function searchProductsByBrand($criteria, array $brandIds, int $paginate = null): array
    {
        $ignoredColumns = ['products_brands.name'];
        $products = Product::withBrand($brandIds)->join('product_brands', 'product_brands.id', 'products.product_brand_id')
            ->join('product_items', 'product_items.product_id', 'products.id')
            ->join('product_images', 'product_images.product_id', 'products.id')
            ->distinct()
            ->where(function ($query) use ($criteria, $ignoredColumns) {
                foreach ($this->columns as $key => $column) {
                    if (in_array($column, $ignoredColumns))
                        continue;
                    if ($column == 'products.ispromo') {
                        if ($criteria['q'] === 'promo')
                            $query->orWhere('ispromo', 1);
                        else
                            $query;
                    } else {
                        $query->orWhere($column, 'LIKE', '%' . $criteria['q'] . '%');
                    }
                }
            })
            ->where('product_items.stock', '>', 0)
            ->withMin('productItems', 'price')
            ->withMax('productItems', 'price')
            ->with(['productBrand:id,name', 'productImages:product_id,image'])
            ->latest();
        if ($paginate !== null) {
            $productsPagination = $products->cursorPaginate($paginate)->withQueryString();
            if (!$productsPagination) {
                return [
                    'code' => 200,
                    'message' => 'Sukses mendapatkan data products',
                    'data' => $productsPagination
                ];
            } else {
                return [
                    'code' => 404,
                    'message' => 'Tidak ada data'
                ];
            }
        }
        $products = $products->get();
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data products',
            'data' => $products
        ];
    }
}
