<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKandangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_kandang' => ['required', 'string', 'max:20', Rule::unique('kandang', 'kode_kandang')->ignore($this->kandang)],
            'nama_kandang' => 'required|string|max:100',
            'alamat_jalan' => 'required|string|max:200',
            'desa_kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kabupaten_kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'kapasitas' => 'required|integer|min:1',
            'tipe_kandang' => 'required|in:baterai,postal,closed_house',
            'status' => 'required|in:aktif,renovasi,nonaktif',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gudang_id' => 'nullable|exists:gudang,id',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kandang.unique' => 'Kode kandang sudah digunakan.',
            'kode_kandang.required' => 'Kode kandang wajib diisi.',
            'nama_kandang.required' => 'Nama kandang wajib diisi.',
            'kapasitas.required' => 'Kapasitas kandang wajib diisi.',
            'kapasitas.min' => 'Kapasitas minimal 1 ekor.',
            'tipe_kandang.required' => 'Tipe kandang wajib dipilih.',
            'status.required' => 'Status kandang wajib dipilih.',
        ];
    }
}
