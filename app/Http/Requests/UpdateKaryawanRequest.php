<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'max:30', Rule::unique('karyawan', 'nik')->ignore($this->karyawan)],
            'nama' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:200',
            'jabatan' => 'required|string|max:50',
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'reset_password' => 'nullable|in:1',
            'password' => 'nullable|string|min:8|confirmed',
            'gudang_id' => 'nullable|exists:gudang,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
        ];
    }
}
