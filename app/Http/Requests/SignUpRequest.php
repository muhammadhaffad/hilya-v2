<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SignUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Auth::guest())
            return true;
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'username' => 'required',
            'password' => 'required|confirmed',
            'fullname' => 'required',
            'phonenumber' => 'required|numeric',
            'province' => 'required',
            'regency' => 'required',
            'district' => 'required',
            'zip' => 'required|numeric',
            'fulladdress' => 'required' 
        ];
    }

    public function attributes()
    {
        return [
            'fullname' => 'nama lengkap',
            'phonenumber' => 'nomor telepon',
            'province' => 'provinsi',
            'regency' => 'kabupaten',
            'district' => 'kecamatan',
            'zip' => 'kode pos',
            'fulladdress' => 'alamat lengkap'
        ];
    }
}
