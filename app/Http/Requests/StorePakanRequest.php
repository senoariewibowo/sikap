<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePakanRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('pakan')?->id;
        return [
            'kode' => 'nullable|string|max:20|unique:pakan,kode,' . $id,
            'nama' => 'required|string|max:100',
            'satuan' => 'required|string|max:20',
            'harga' => 'nullable|numeric|min:0',
            'stok_minimal' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }
}
