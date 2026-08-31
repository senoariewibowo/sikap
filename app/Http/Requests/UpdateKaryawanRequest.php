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

            // reset password (untuk karyawan yang SUDAH punya akun)
            'reset_password' => 'nullable|in:1',

            // buat akun baru (untuk karyawan yang BELUM punya akun)
            'buat_akun' => 'nullable|in:1',
            'email' => ['nullable', 'required_if:buat_akun,1', 'email', 'max:100', Rule::unique('users', 'email')],
            'role_id' => ['nullable', 'required_if:buat_akun,1', 'exists:roles,id'],

            // dipakai bareng utk reset_password ATAU buat_akun
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'required_if:buat_akun,1'],

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
            'email.required_if' => 'Email wajib diisi jika membuat akun login.',
            'email.unique' => 'Email sudah digunakan.',
            'role_id.required_if' => 'Role wajib dipilih jika membuat akun login.',
            'password.required_if' => 'Password wajib diisi jika membuat akun login.',
        ];
    }
}
