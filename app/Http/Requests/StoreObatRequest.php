<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreObatRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('obat')?->id;
        return [
            'kode' => 'nullable|string|max:20|unique:obat,kode,' . $id,
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:obat,vitamin',
            'satuan' => 'required|string|max:20',
            'stok_minimal' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }
}
