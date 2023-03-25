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
            ->withMax('productItems', 'price');
        if ($offset !== null && $limit !== null) {
            $productsLimit = $products->offset($offset)->limit($limit)->get();
            return array(
                'code' => $productsLimit ? 200 : 404,
                'data' => $productsLimit->all()
            );
        }
        if ($paginate !== null) {
            $productsPagination = $products->simplePaginate($paginate)->withQueryString();
            return array(
                'code' => $productsPagination->items() ? 200 : 404,
                'data' => $productsPagination->items(),
                'page' => array(
                    'total' => $products->count(),
                    'total_per_pages' => $paginate,
                    'total_pages' => (int) ceil($products->count() / $paginate),
                    'previous_page_url' => $productsPagination->previousPageUrl(),
                    'current_page' => $productsPagination->currentPage(),
                    'next_page_url' => $productsPagination->nextPageUrl(),
                )
            );
        }
        $products = $products->get();
        return array(
            'code' => $products->all() ? 200 : 404,
            'data' => $products->all()
        );
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
            ->withMax('productItems', 'price');
        if ($offset !== null && $limit !== null) {
            $productsLimit = $products->offset($offset)->limit($limit)->get();
            return array(
                'code' => $productsLimit ? 200 : 404,
                'data' => $productsLimit->all()
            );
        }
        if ($paginate !== null) {
            $productsPagination = $products->simplePaginate($paginate)->withQueryString();
            return array(
                'code' => $productsPagination->items() ? 200 : 404,
                'data' => $productsPagination->items(),
                'page' => array(
                    'total' => $products->count(),
                    'total_per_pages' => $paginate,
                    'total_pages' => (int) ceil($products->count() / $paginate),
                    'previous_page_url' => $productsPagination->previousPageUrl(),
                    'current_page' => $productsPagination->currentPage(),
                    'next_page_url' => $productsPagination->nextPageUrl(),
                )
            );
        }
        $products = $products->get();
        return array(
            'code' => $products->all() ? 200 : 404,
            'data' => $products->all()
        );
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
            ->withMax('productItems', 'price');
        if ($offset !== null && $limit !== null) {
            $productsLimit = $products->offset($offset)->limit($limit)->get();
            return array(
                'code' => $productsLimit ? 200 : 404,
                'data' => $productsLimit->all()
            );
        }
        if ($paginate !== null) {
            $productsPagination = $products->simplePaginate($paginate)->withQueryString();
            return array(
                'code' => $productsPagination->items() ? 200 : 404,
                'data' => $productsPagination->items(),
                'page' => array(
                    'total' => $products->count(),
                    'total_per_pages' => $paginate,
                    'total_pages' => (int) ceil($products->count() / $paginate),
                    'previous_page_url' => $productsPagination->previousPageUrl(),
                    'current_page' => $productsPagination->currentPage(),
                    'next_page_url' => $productsPagination->nextPageUrl(),
                )
            );
        }
        $products = $products->get();
        return array(
            'code' => $products->all() ? 200 : 404,
            'data' => $products->all()
        );
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
            ->withMax('productItems', 'price');
        if ($offset !== null && $limit !== null) {
            $productsLimit = $products->offset($offset)->limit($limit)->get();
            return array(
                'code' => $productsLimit->all() ? 200 : 404,
                'data' => $productsLimit->all()
            );
        }
        if ($paginate !== null) {
            $productsPagination = $products->simplePaginate($paginate)->withQueryString();
            return array(
                'code' => $productsPagination->items() ? 200 : 404,
                'data' => $productsPagination->items(),
                'page' => array(
                    'total' => $products->count(),
                    'total_per_pages' => $paginate,
                    'total_pages' => (int) ceil($products->count() / $paginate),
                    'previous_page_url' => $productsPagination->previousPageUrl(),
                    'current_page' => $productsPagination->currentPage(),
                    'next_page_url' => $productsPagination->nextPageUrl(),
                )
            );
        }
        $products = $products->get();
        return array(
            'code' => $products->all(),
            'data' => $products->all()
        );
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
            ->with(['productBrand:id,name', 'productImages:product_id,image']);
        if ($offset !== null && $limit !== null) {
            $productsLimit = $products->offset($offset)->limit($limit)->get();
            return array(
                'code' => $productsLimit->all() ? 200 : 404,
                'data' => $productsLimit->all()
            );
        }
        $products = $products->get();
        return array(
            'code' => $products->all() ? 200 : 404,
            'data' => $products->all()
        );
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
            ->with(['productBrand:id,name', 'productImages:product_id,image']);
        if ($paginate !== null) {
            $productsPagination = $products->simplePaginate($paginate)->withQueryString();
            return array(
                'code' => $productsPagination->items() ? 200 : 404,
                'data' => $productsPagination->items(),
                'page' => array(
                    'total' => $products->count(),
                    'total_per_pages' => $paginate,
                    'total_pages' => (int) ceil($products->count() / $paginate),
                    'previous_page_url' => $productsPagination->previousPageUrl(),
                    'current_page' => $productsPagination->currentPage(),
                    'next_page_url' => $productsPagination->nextPageUrl(),
                )
            );
        }
        $products = $products->get();
        return array(
            'code' => $products->all() ? 200 : 404,
            'data' => $products->all()
        );
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
            ->with(['productBrand:id,name', 'productImages:product_id,image']);
        if ($paginate !== null) {
            $productsPagination = $products->simplePaginate($paginate)->withQueryString();
            return array(
                'code' => $productsPagination->items() ? 200 : 404,
                'data' => $productsPagination->items(),
                'page' => array(
                    'total' => $products->count(),
                    'total_per_pages' => $paginate,
                    'total_pages' => (int) ceil($products->count() / $paginate),
                    'previous_page_url' => $productsPagination->previousPageUrl(),
                    'current_page' => $productsPagination->currentPage(),
                    'next_page_url' => $productsPagination->nextPageUrl(),
                )
            );
        }
        $products = $products->get();
        return array(
            'code' => $products->all() ? 200 : 404,
            'data' => $products->all()
        );
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
            ->with(['productBrand:id,name', 'productImages:product_id,image']);
        if ($paginate !== null) {
            $productsPagination = $products->simplePaginate($paginate)->withQueryString();
            return array(
                'code' => $productsPagination->items() ? 200 : 404,
                'data' => $productsPagination->items(),
                'page' => array(
                    'total' => $products->count(),
                    'total_per_pages' => $paginate,
                    'total_pages' => (int) ceil($products->count() / $paginate),
                    'previous_page_url' => $productsPagination->previousPageUrl(),
                    'current_page' => $productsPagination->currentPage(),
                    'next_page_url' => $productsPagination->nextPageUrl(),
                )
            );
        }
        $products = $products->get();
        return array(
            'code' => $products->all() ? 200 : 404,
            'data' => $products->all()
        );
    }
}
