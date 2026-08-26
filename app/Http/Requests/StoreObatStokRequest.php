<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreObatStokRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'obat_id' => 'required|exists:obat,id',
            'gudang_id' => 'required|exists:gudang,id',
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ];
    }
}
