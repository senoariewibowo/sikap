<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePopulasiAyamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kandang_id' => 'required|exists:kandang,id',
            'tanggal' => 'required|date',
            'jumlah_masuk' => 'required|integer|min:0',
            'jumlah_mati' => 'required|integer|min:0',
            'jumlah_afkir' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'kandang_id.required' => 'Kandang wajib dipilih.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'jumlah_masuk.required' => 'Jumlah masuk wajib diisi.',
            'jumlah_mati.required' => 'Jumlah mati wajib diisi.',
            'jumlah_afkir.required' => 'Jumlah afkir wajib diisi.',
        ];
    }
}
