@extends('layouts.admin')

@section('title', 'Edit User - SIKAP')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Edit User: {{ $user->name }}</h2>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" :value="'Nama Lengkap'" />
                <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $user->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="'Email'" />
                <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $user->email)" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="role_id" :value="'Role'" />
                <select id="role_id" name="role_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required onchange="toggleGudangField(this)">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role->nama_role)) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
            </div>

            <div id="gudang_field" class="hidden">
                <x-input-label for="gudang_id" :value="'Gudang (untuk petugas gudang)'" />
                <select id="gudang_id" name="gudang_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                    <option value="">Pilih Gudang</option>
                    @foreach($gudangs as $g)
                        <option value="{{ $g->id }}" {{ old('gudang_id', $user->gudang_id) == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('gudang_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="'Password Baru (kosongkan jika tidak diubah)'" />
                <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="'Konfirmasi Password Baru'" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </div>
</div>

@php
    $petugasGudangRoleId = \App\Models\Role::where('nama_role', 'petugas_gudang')->value('id');
@endphp
<script>
function toggleGudangField(select) {
    var gudangField = document.getElementById('gudang_field');
    var petugasGudangId = '{{ $petugasGudangRoleId }}';
    if (select.value === petugasGudangId) {
        gudangField.classList.remove('hidden');
        document.getElementById('gudang_id').required = true;
    } else {
        gudangField.classList.add('hidden');
        document.getElementById('gudang_id').value = '';
        document.getElementById('gudang_id').required = false;
    }
}
document.addEventListener('DOMContentLoaded', function() {
    toggleGudangField(document.getElementById('role_id'));
});
</script>
@endsection
