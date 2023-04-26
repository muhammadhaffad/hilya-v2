<?php
namespace App\Services\Account;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountServiceImplement implements AccountService 
{
    public function account()
    {
        return [
            'code' => 200,
            'message' => 'Sukses mendapatkan data profil',
            'data' => auth()->user()
        ];
    }
    public function updateProfile($attr)
    {
        $validator = Validator::make($attr, [
            'email' => 'required|email',
            'fullname' => 'required|string',
            'phonenumber' => 'required|numeric',
            'password' => 'current_password'
        ], [
            'required' => 'Data :attribute wajib diisi.',
            'email' => 'Data :attribute wajib berupa email.',
            'string' => 'Data :attribute wajib berupa teks.',
            'numeric' => 'Data :attribute wajib berupa angka.',
            'current_password' => 'Password tidak sesuai'
        ], [
            'fullname' => 'nama lengkap',
            'phonenumber' => 'nomor telepon'
        ]);
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $validator->errors()
            ];
        }
        DB::beginTransaction();
        try {
            auth()->user()->update([
                'email' => $attr['email'],
                'fullname' => $attr['fullname'],
                'phonenumber' => $attr['phonenumber']
            ]);
            DB::commit();
            return [
                'code' => 204,
                'message' => 'Profil berhasil diubah'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function changePassword($attr)
    {
        $validator = Validator::make($attr, [
            'old_password' => 'current_password',
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ], [
            'required' => 'Data :attribute wajib diisi.',
            'confirmed' => 'Password yang dimasukkan tidak sama',
            'current_password' => 'Password tidak sesuai'
        ], [
            'old_password' => 'password lama',
            'new_password' => 'password baru',
            'new_password_confirmation' => 'konfirmasi password baru'
        ]);
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $validator->errors()
            ];
        }
        auth()->user()->update([
            'password' => Hash::make($attr['new_password'])
        ]);
        return [
            'code' => 204,
            'message' => 'Password berhasil diperbarui'
        ];
    }
}