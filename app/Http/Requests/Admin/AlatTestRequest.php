<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AlatTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'stock' => 'required|integer',
            'serial_number.0' => 'required|string', // hanya validasi textarea pertama
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function message()
    {
        return [
            'name.required' => 'Nama alat wajib diisi.',
            'name.unique' => 'Nama alat sudah terdaftar.',
            'stock.required' => 'Jumlah stok harus diisi.',
            'stock.integer' => 'Stok harus berupa angka.',
            'stock.min' => 'Stok minimal 1.'
        ];
    }
}
