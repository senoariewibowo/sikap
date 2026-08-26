<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisPakanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'kategori' => 'required|in:pakan,obat,vitamin',
            'satuan' => 'required|string|max:20',
            'stok_minimal' => 'nullable|numeric|min:0',
            'harga' => 'nullable|numeric|min:0',
        ];
    }
}
