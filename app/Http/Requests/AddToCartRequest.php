<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'product_detail_id' => 'required|numeric',
            'qty' => 'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'product_detail_id' => 'produk',
            'qty' => 'jumlah'
        ];
    }
}
