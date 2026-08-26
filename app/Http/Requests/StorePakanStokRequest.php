<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePakanStokRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pakan_id' => 'required|exists:pakan,id',
            'gudang_id' => 'required|exists:gudang,id',
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ];
    }
}
