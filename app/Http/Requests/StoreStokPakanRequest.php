<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStokPakanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kandang_id' => 'nullable|exists:kandang,id',
            'jenis_pakan_id' => 'required|exists:jenis_pakan,id',
            'tipe' => 'required|in:masuk,keluar',
            'jumlah_kg' => 'required|numeric|min:0.01',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_pakan_id.required' => 'Jenis pakan/obat wajib dipilih.',
            'tipe.required' => 'Tipe transaksi wajib dipilih.',
            'jumlah_kg.required' => 'Jumlah wajib diisi.',
            'jumlah_kg.min' => 'Jumlah minimal 0.01 kg.',
        ];
    }
}
