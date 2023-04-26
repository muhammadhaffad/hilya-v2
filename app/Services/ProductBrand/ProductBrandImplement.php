<?php
namespace App\Services\ProductBrand;

use App\Models\ProductBrand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function updateBrand($id, $attr): array
    {
        $validator = Validator::make($attr, [
            'image' => 'sometimes|image',
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $validator->errors()
            ];
        }
        $brand = ProductBrand::find($id);
        if ($brand) {
            if (isset($attr['image']) && $attr['image'] !== null) {
                $brand->update([
                    'image' => $attr['image']->store('public/brand-images'),
                ]);
            }

            $brand->update([
                'name' => $attr['name']
            ]);
            return [
                'code' => 204,
                'message' => 'Brand berhasil diupdate'
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
    }

    public function storeBrand($attr): array
    {
        $validator = Validator::make($attr, [
            'image' => 'required|image',
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $validator->errors()
            ];
        }
        $brand = ProductBrand::create([
            'image' => $attr['image']->store('public/brand-images'),
            'name' => $attr['name']
        ]);
        if ($brand) {
            return [
                'code' => 204,
                'message' => 'Brand berhasil ditambah'
            ];
        } else {
            return [
                'code' => 404,
                'message' => 'Tidak ada data'
            ];
        }
    }

    public function deleteBrand($id): array
    {
        if (!ProductBrand::find($id)) {
           return [
            'code' => 404,
            'message' => 'Tidak ada data'
           ];
        }
        DB::beginTransaction();
        try {
            $deleted = ProductBrand::find($id)->delete();
            if ($deleted) {
                DB::commit();
                return [
                    'code' => 204,
                    'message' => 'Brand berhasil dihapus'
                ];
            } else {
                DB::rollBack();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}