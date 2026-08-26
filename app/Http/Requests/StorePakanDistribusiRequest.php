<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePakanDistribusiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pakan_id' => 'required|exists:pakan,id',
            'gudang_id' => 'required|exists:gudang,id',
            'kandang_id' => 'required|exists:kandang,id',
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal_kirim' => 'required|date',
        ];
    }
}
