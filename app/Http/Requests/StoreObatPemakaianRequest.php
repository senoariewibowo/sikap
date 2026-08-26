<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreObatPemakaianRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'obat_id' => 'required|exists:obat,id',
            'kandang_id' => 'required|exists:kandang,id',
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ];
    }
}
