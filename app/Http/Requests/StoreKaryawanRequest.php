<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => 'required|string|max:30|unique:karyawan,nik',
            'nama' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:200',
            'jabatan' => 'required|string|max:50',
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'buat_akun' => 'nullable|in:1',
            'email' => 'required_if:buat_akun,1|nullable|string|email|max:255|unique:users,email',
            'password' => 'required_if:buat_akun,1|nullable|string|min:8|confirmed',
            'role_id' => 'required_if:buat_akun,1|nullable|exists:roles,id',
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
            'email.required_if' => 'Email wajib diisi jika membuat akun.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required_if' => 'Password wajib diisi jika membuat akun.',
            'password.min' => 'Password minimal 8 karakter.',
            'role_id.required_if' => 'Role wajib dipilih jika membuat akun.',
        ];
    }
}
