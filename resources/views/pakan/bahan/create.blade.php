@extends('layouts.admin')

@section('title', 'Tambah Bahan Pakan - SIKAP')
@section('page-title', 'Tambah Bahan Pakan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Tambah Bahan Pakan</h2>
        </div>

        <form action="{{ route('pakan.bahan.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="kode" :value="'Kode (otomatis)'" />
                    <x-text-input id="kode" name="kode" class="block mt-1 w-full bg-gray-100 cursor-not-allowed" :value="old('kode', $nextKode)" readonly required />
                    <x-input-error :messages="$errors->get('kode')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="nama" :value="'Nama Bahan'" />
                    <x-text-input id="nama" name="nama" class="block mt-1 w-full" :value="old('nama')" required />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="satuan" :value="'Satuan'" />
                    <select id="satuan" name="satuan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>kg</option>
                        <option value="sak" {{ old('satuan') == 'sak' ? 'selected' : '' }}>Sak</option>
                        <option value="liter" {{ old('satuan') == 'liter' ? 'selected' : '' }}>Liter</option>
                        <option value="gram" {{ old('satuan') == 'gram' ? 'selected' : '' }}>Gram</option>
                    </select>
                    <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="harga" :value="'Harga per Satuan (Rp)'" />
                    <x-text-input id="harga" name="harga" type="number" step="0.01" class="block mt-1 w-full" :value="old('harga')" />
                    <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="stok_minimal" :value="'Stok Minimal'" />
                    <x-text-input id="stok_minimal" name="stok_minimal" type="number" step="0.01" class="block mt-1 w-full" :value="old('stok_minimal')" />
                    <x-input-error :messages="$errors->get('stok_minimal')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="status" :value="'Status'" />
                <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pakan.bahan.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
