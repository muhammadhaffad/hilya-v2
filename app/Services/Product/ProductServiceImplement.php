<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductItem;
use App\Models\ProductOrigin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
            if ($productsPagination) {
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
            if ($productsPagination) {
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
            if ($productsPagination) {
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
            'productBrand',
            'productImages',
            'productItems' => [
                'productOrigins'
            ], 
            'productOrigins'
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
        if (@$criteria['q'] == null) 
            $criteria['q'] = '';
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
            ->with(['productBrand:id,name', 'productImage', 'productItems'])
            ->latest();
        if ($limit !== null) {
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
        $products = $products->limit(40)->get();
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

    public function createProduct($attr): array
    {
        $validator = Validator::make($attr, 
        /* Rules */
        [
            'product_images' => 'required|array',
            'product_images.*' => 'required|image|max:5000',
            'product_info.name' => 'required|string',
            'product_info.product_brand_id' => 'required|numeric',
            'product_info.availability' => ['required', Rule::in(['ready', 'pre-order'])],
            'product_info.ispromo' => 'sometimes|boolean',
            'product_info.discount' => 'sometimes|numeric|min:1|max:100',
            'product_info.category' => 'required|string',
            'product_info.description' => 'required|string',
            'product_origins' => 'sometimes|array',
            'product_origins.*.index' => 'sometimes|numeric',
            'product_origins.*.name' => 'sometimes|string',
            'product_origins.*.stock' => 'sometimes|numeric|min:1',
            'product_items' => 'required|array',
            'product_items.*.gender' => ['required', Rule::in([0,1,2,3])],
            'product_items.*.size' => 'required|string',
            'product_items.*.color' => 'required|string',
            'product_items.*.price' => 'required|numeric|min:1',
            'product_items.*.note_bene' => 'sometimes|string',
            'product_items.*.stock' => 'required|numeric|min:1',
            'product_items.*.product_origins' => 'sometimes|array',
            'product_items.*.product_origins.*' => 'sometimes|numeric',
        ],
        /* Pesan Error */
        [
            'required' => 'Data :attribute wajib diisi',
            'array' => 'Data :attribute harus berupa array',
            'image' => 'Data :attribute harus berupa gambar',
            'product_images.*.max' => 'Gambar tidak boleh lebih dari 5 MB',
            'string' => 'Data :attribute harus berupa text',
            'numeric' => 'Data :attribute harus berupa angka',
            'boolean' => 'Data :attribute harus berupa boolean',
            'product_info.discount.min' => 'Diskon minimal 1 %',
            'product_info.discount.max' => 'Diskon maksimal 100 %',
            'product_origins.*.stock.min' => 'Data :attribute minimal 1',
            'product_items.*.price.min' => 'Data :attribute minimal Rp1',
            'product_items.*.stock.min' => 'Data :attribute minimal 1'
        ],
        /* Attributes */
        [
            'product_images' => 'gambar produk',
            'product_images.*' => 'gambar produk',
            'product_info.name' => 'nama produk',
            'product_info.product_brand_id' => 'brand produk',
            'product_info.availability' => 'status produk',
            'product_info.ispromo' => 'promo',
            'product_info.discount' => 'diskon',
            'product_info.category' => 'kategori produk',
            'product_info.description' => 'deskripsi produk',
            'product_origins' => 'produk individu',
            'product_origins.*.index' => 'id/index produk individu',
            'product_origins.*.name' => 'nama produk individu',
            'product_origins.*.stock' => 'stok produk individu',
            'product_items' => 'produk item',
            'product_items.*.gender' => 'gender',
            'product_items.*.size' => 'ukuran produk item',
            'product_items.*.color' => 'warna produk item',
            'product_items.*.price' => 'harga produk item',
            'product_items.*.note_bene' => 'keterangan produk item',
            'product_items.*.stock' => 'stok produk item',
            'product_items.*.product_origins' => 'produk individu dari produk item',
            'product_items.*.product_origins.*' => 'produk individu dari produk item',
        ]
        );
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid',
                'bag' => 'productCreate',
                'errors' => $validator->errors()
            ];
        }
        DB::beginTransaction();
        try {
            if (!isset($attr['product_info']['ispromo']))
                $attr['product_info']['ispromo'] = false;
            $product = Product::create($attr['product_info']);
            
            /* Menyimpan gambar produk */
            $productImages = [];
            $images = $attr['product_images'];
            foreach ($images as $image) {
                $productImages[] = [
                    'image' => $image->store('public/product-images')
                ];
            }
            $product->productImages()->createMany($productImages);
            
            /* Menyimpan product origins jika ada, dan melakukan mapping array key dari attr[product_origins] ke id productOrigins */
            if (isset($attr['product_origins'])) {
                $productOriginIds = $product->productOrigins()->createMany($attr['product_origins'])->pluck('id');
                $attr['product_origins'] = collect(array_keys($attr['product_origins']))->combine($productOriginIds)->toArray();
            }
            foreach ($attr['product_items'] as $attrProductItem) {
                if ($attr['product_info']['ispromo'] == 1) {
                    $attrProductItem['discount'] = $attr['product_info']['discount'];
                }
                if (isset($attrProductItem['product_origins'])) {
                    $attrProductItem['is_bundle'] = true;
                    $attrProductItem['product_origins'] = collect($attr['product_origins'])->only($attrProductItem['product_origins'])->toArray();
                } else {
                    $attrProductItem['is_bundle'] = false;
                }
                switch ($attrProductItem['gender']) {
                    case '0':
                        $attrProductItem['gender'] = 'Perempuan';
                        $attrProductItem['age'] = 'Dewasa';
                        break;
                    case '1':
                        $attrProductItem['gender'] = 'Laki-laki';
                        $attrProductItem['age'] = 'Dewasa';
                        break;
                    case '2':
                        $attrProductItem['gender'] = 'Perempuan';
                        $attrProductItem['age'] = 'Anak';
                        break;
                    case '3':
                        $attrProductItem['gender'] = 'Laki-laki';
                        $attrProductItem['age'] = 'Anak';
                        break;
                }
                /* Menyimpan product item */
                $productItem = $product->productItems()->create($attrProductItem);
                if (isset($attrProductItem['product_origins'])) {
                    /* Jika product item bundle maka attach product item tersebut ke product origin */
                    $productItem->productOrigins()->attach($attrProductItem['product_origins']);
                }
                DB::commit();
                return [
                    'code' => 204,
                    'message' => 'Sukses membuat produk',
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateProduct($id, $attr): array
    {
        $validator = Validator::make($attr, 
        /* Rules */
        [
            'product_images' => 'required|array',
            'product_images.*.file' => 'sometimes|image|max:5000',
            'product_info.name' => 'required|string',
            'product_info.product_brand_id' => 'required|numeric',
            'product_info.availability' => ['required', Rule::in(['ready', 'pre-order'])],
            'product_info.ispromo' => 'sometimes|boolean',
            'product_info.discount' => 'sometimes|numeric|min:1|max:100',
            'product_info.category' => 'required|string',
            'product_info.description' => 'required|string',
            'product_origins' => 'sometimes|array',
            'product_origins.*.index' => 'sometimes|numeric',
            'product_origins.*.name' => 'sometimes|string',
            'product_origins.*.stock' => 'sometimes|numeric|min:0',
            'product_items' => 'required|array',
            'product_items.*.gender' => ['required', Rule::in([0,1,2,3])],
            'product_items.*.size' => 'required|string',
            'product_items.*.color' => 'required|string',
            'product_items.*.price' => 'required|numeric|min:1',
            'product_items.*.note_bene' => 'sometimes|string',
            'product_items.*.stock' => 'required|numeric|min:0',
            'product_items.*.product_origins' => 'sometimes|array',
            'product_items.*.product_origins.*' => 'sometimes|numeric',
        ],
        /* Pesan Error */
        [
            'required' => 'Data :attribute wajib diisi',
            'array' => 'Data :attribute harus berupa array',
            'image' => 'Data :attribute harus berupa gambar',
            'product_images.*.max' => 'Gambar tidak boleh lebih dari 5 MB',
            'string' => 'Data :attribute harus berupa text',
            'numeric' => 'Data :attribute harus berupa angka',
            'boolean' => 'Data :attribute harus berupa boolean',
            'product_info.discount.min' => 'Diskon minimal 1 %',
            'product_info.discount.max' => 'Diskon maksimal 100 %',
            'product_origins.*.stock.min' => 'Data :attribute minimal 1',
            'product_items.*.price.min' => 'Data :attribute minimal Rp1',
            'product_items.*.stock.min' => 'Data :attribute minimal 1'
        ],
        /* Attributes */
        [
            'product_images' => 'gambar produk',
            'product_images.*' => 'gambar produk',
            'product_info.name' => 'nama produk',
            'product_info.product_brand_id' => 'brand produk',
            'product_info.availability' => 'status produk',
            'product_info.ispromo' => 'promo',
            'product_info.discount' => 'diskon',
            'product_info.category' => 'kategori produk',
            'product_info.description' => 'deskripsi produk',
            'product_origins' => 'produk individu',
            'product_origins.*.index' => 'id/index produk individu',
            'product_origins.*.name' => 'nama produk individu',
            'product_origins.*.stock' => 'stok produk individu',
            'product_items' => 'produk item',
            'product_items.*.gender' => 'gender',
            'product_items.*.size' => 'ukuran produk item',
            'product_items.*.color' => 'warna produk item',
            'product_items.*.price' => 'harga produk item',
            'product_items.*.note_bene' => 'keterangan produk item',
            'product_items.*.stock' => 'stok produk item',
            'product_items.*.product_origins' => 'produk individu dari produk item',
            'product_items.*.product_origins.*' => 'produk individu dari produk item',
        ]
        );
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid',
                'bag' => 'productUpdate',
                'errors' => $validator->errors()
            ];
        }
        DB::beginTransaction();
        try {
            if (!isset($attr['product_info']['ispromo']))
                $attr['product_info']['ispromo'] = false;
            $product = Product::find($id)->update($attr['product_info']);
            
            /* Mengupdate gambar produk */
            $productImagesDeleted = Product::find($id)->productImages()->get()->pluck('id')->diff(array_column(@$attr['product_images'] ?? [], 'id'));
            if ($productImagesDeleted != []) {
                ProductImage::destroy($productImagesDeleted);
            }
            $productImages = [];
            $images = $attr['product_images'];
            foreach ($images as $image) {
                if (isset($image['file'])) {
                    $productImages[] = [
                        'id' => $image['id'],
                        'product_id' => $id,
                        'image' => $image['file']->store('public/product-images')
                    ];
                }
            }
            ProductImage::upsert($productImages, 'id');
            
            $productOriginsDeleted = Product::find($id)->productOrigins()->get()->pluck('id')->diff(array_column($attr['product_origins'] ?? [], 'id'));
            if ($productOriginsDeleted != []) {
                ProductOrigin::destroy($productOriginsDeleted);
            }
            ProductOrigin::upsert(array_map(function ($item) use ($id) {
                return array_merge($item, ['product_id' => $id]); /* Id product */
            }, @$attr['product_origins'] ?? []), ['id']);
            if (isset($attr['product_origins'])) {
                $productOriginIds = Product::find($id)->productOrigins()->pluck('id');
                $attr['product_origins'] = collect(array_column($attr['product_origins'], 'id'))->combine($productOriginIds)->toArray();
            }
            $productItemsDeleted = Product::find($id)->productItems()->get()->pluck('id')->diff(array_column($attr['product_items'], 'id'));
            if ($productItemsDeleted != []) {
                ProductItem::destroy($productItemsDeleted);
            }
            $productItems = [];
            foreach ($attr['product_items'] as $attrProductItem) {
                if (isset($attrProductItem['product_origins'])) {
                    $attrProductItem['is_bundle'] = true;
                } else {
                    $attrProductItem['is_bundle'] = false;
                }
                if ($attr['product_info']['ispromo'] == 1) {
                    $attrProductItem['discount'] = $attr['product_info']['discount'];
                } else {
                    $attrProductItem['discount'] = 0;
                }
                switch ($attrProductItem['gender']) {
                    case '0':
                        $attrProductItem['gender'] = 'Perempuan';
                        $attrProductItem['age'] = 'Dewasa';
                        break;
                    case '1':
                        $attrProductItem['gender'] = 'Laki-laki';
                        $attrProductItem['age'] = 'Dewasa';
                        break;
                    case '2':
                        $attrProductItem['gender'] = 'Perempuan';
                        $attrProductItem['age'] = 'Anak';
                        break;
                    case '3':
                        $attrProductItem['gender'] = 'Laki-laki';
                        $attrProductItem['age'] = 'Anak';
                        break;
                }
                $attrProductItem['product_id'] = $id;
                unset($attrProductItem['product_origins']);
                $productItems[] = $attrProductItem;
            }
            ProductItem::upsert($productItems, ['id']);
            foreach (array_map(null, $attr['product_items'], Product::find($id)->productItems()->get()->all()) as $item) {
                list($attrProductItem, $productItem) = $item;
                if (isset($attrProductItem['product_origins'])) {
                    $attrProductItem['product_origins'] = collect($attr['product_origins'])->only($attrProductItem['product_origins'])->toArray();
                    $productItem->productOrigins()->detach();
                    $productItem->productOrigins()->attach($attrProductItem['product_origins'], ['updated_at' => now()]);
                }
            }
            DB::commit();
            return [
                'code' => 204,
                'message' => 'Sukses memperbarui produk'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function showProduct($id): array
    {
        $product = Product::find($id)?->load([
            'productBrand',
            'productImages',
            'productItems' => [
                'productOrigins'
            ], 
            'productOrigins'
        ]);
        if ($product) {
            return [
                'code' => 200,
                'message' => 'Sukses mendapatkan data',
                'data' => $product
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
    }

    public function deleteProduct($id): array
    {
        if (Product::find($id)) {
            DB::beginTransaction();
            try {
                Product::find($id)->productOrigins()->delete();
                Product::find($id)->productItems()->delete();
                Product::find($id)->productImages()->delete();
                Product::find($id)->delete();
                DB::commit();
                return [
                    'code' => 204,
                    'message' => 'Produk berhasil dihapus!'
                ];
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
    }
}
