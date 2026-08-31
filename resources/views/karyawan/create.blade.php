@extends('layouts.admin')

@section('title', 'Tambah Karyawan - SIKAP')
@section('page-title', 'Tambah Karyawan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Tambah Karyawan</h2>
        </div>

        <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="nik" :value="'NIK'" />
                    <x-text-input id="nik" name="nik" class="block mt-1 w-full" :value="old('nik')" required />
                    <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="nama" :value="'Nama Lengkap'" />
                    <x-text-input id="nama" name="nama" class="block mt-1 w-full" :value="old('nama')" required />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="no_hp" :value="'No. HP'" />
                    <x-text-input id="no_hp" name="no_hp" class="block mt-1 w-full" :value="old('no_hp')" />
                    <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="jabatan" :value="'Jabatan'" />
                    <select id="jabatan" name="jabatan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Jabatan</option>
                        <option value="Manajer Kandang" {{ old('jabatan') == 'Manajer Kandang' ? 'selected' : '' }}>Manajer Kandang</option>
                        <option value="Petugas Kandang" {{ old('jabatan') == 'Petugas Kandang' ? 'selected' : '' }}>Petugas Kandang</option>
                        <option value="Teknisi" {{ old('jabatan') == 'Teknisi' ? 'selected' : '' }}>Teknisi</option>
                        <option value="Admin Gudang" {{ old('jabatan') == 'Admin Gudang' ? 'selected' : '' }}>Admin Gudang</option>
                        <option value="Petugas Gudang" {{ old('jabatan') == 'Petugas Gudang' ? 'selected' : '' }}>Petugas Gudang</option>
                        <option value="Lainnya" {{ old('jabatan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="alamat" :value="'Alamat'" />
                <x-text-input id="alamat" name="alamat" class="block mt-1 w-full" :value="old('alamat')" />
                <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="tanggal_masuk" :value="'Tanggal Masuk Kerja'" />
                    <x-text-input id="tanggal_masuk" name="tanggal_masuk" type="date" class="block mt-1 w-full" :value="old('tanggal_masuk')" required />
                    <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="status" :value="'Status'" />
                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                        <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="foto" :value="'Foto (opsional, max 2MB)'" />
                <input id="foto" name="foto" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <x-input-error :messages="$errors->get('foto')" class="mt-2" />
            </div>

            <div class="border-t pt-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="buat_akun" name="buat_akun" value="1" {{ old('buat_akun') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Buat akun login untuk karyawan ini</span>
                </label>

                <div id="akunFields" class="mt-4 space-y-4 {{ old('buat_akun') ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="email" :value="'Email'" />
                            <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="role_id" :value="'Role'" />
                            <select id="role_id" name="role_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->nama_role)) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="password" :value="'Password'" />
                        <div class="relative mt-1">
                            <x-text-input id="password" name="password" type="password" class="block w-full pr-10" />
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg id="eyeIconShow" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eyeIconHide" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.066 7.5a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter.</p>
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="'Konfirmasi Password'" />
                        <div class="relative mt-1">
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full pr-10" />
                            <button type="button" id="togglePasswordConfirmation" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg id="eyeIconShowConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eyeIconHideConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.066 7.5a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <p id="passwordMismatchError" class="hidden text-xs text-red-500 mt-1">Password dan konfirmasi password tidak sama.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('karyawan.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('buat_akun').addEventListener('change', function() {
    document.getElementById('akunFields').classList.toggle('hidden', !this.checked);
});

var petugasRoleId = '{{ \App\Models\Role::where("nama_role", "petugas")->value("id") }}';
var petugasGudangRoleId = '{{ \App\Models\Role::where("nama_role", "petugas_gudang")->value("id") }}';
var viewerRoleId = '{{ \App\Models\Role::where("nama_role", "viewer")->value("id") }}';

var roleSelect = document.getElementById('role_id');

var jabatanRoleMap = {
    'Manajer Kandang': petugasRoleId,
    'Petugas Kandang': petugasRoleId,
    'Teknisi': petugasRoleId,
    'Admin Gudang': petugasGudangRoleId,
    'Petugas Gudang': petugasGudangRoleId,
    'Lainnya': viewerRoleId
};

document.getElementById('jabatan').addEventListener('change', function() {
    var roleId = jabatanRoleMap[this.value] || '';
    roleSelect.value = roleId;
});

var togglePassword = document.getElementById('togglePassword');
var passwordInput = document.getElementById('password');
var eyeShow = document.getElementById('eyeIconShow');
var eyeHide = document.getElementById('eyeIconHide');

togglePassword.addEventListener('click', function() {
    var isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    eyeShow.classList.toggle('hidden', isPassword);
    eyeHide.classList.toggle('hidden', !isPassword);
});

var togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
var passwordConfirmInput = document.getElementById('password_confirmation');
var eyeShowConfirm = document.getElementById('eyeIconShowConfirm');
var eyeHideConfirm = document.getElementById('eyeIconHideConfirm');

togglePasswordConfirmation.addEventListener('click', function() {
    var isPassword = passwordConfirmInput.type === 'password';
    passwordConfirmInput.type = isPassword ? 'text' : 'password';
    eyeShowConfirm.classList.toggle('hidden', isPassword);
    eyeHideConfirm.classList.toggle('hidden', !isPassword);
});

var passwordMismatchError = document.getElementById('passwordMismatchError');

function checkPasswordMatch() {
    if (passwordConfirmInput.value.length > 0 && passwordInput.value !== passwordConfirmInput.value) {
        passwordMismatchError.classList.remove('hidden');
    } else {
        passwordMismatchError.classList.add('hidden');
    }
}

passwordInput.addEventListener('input', checkPasswordMatch);
passwordConfirmInput.addEventListener('input', checkPasswordMatch);

document.querySelector('form').addEventListener('submit', function(e) {
    if (document.getElementById('buat_akun').checked && passwordInput.value !== passwordConfirmInput.value) {
        e.preventDefault();
        checkPasswordMatch();
    }
});
</script>
@endsection