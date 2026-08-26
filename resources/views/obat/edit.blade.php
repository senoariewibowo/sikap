@extends('layouts.admin')

@section('title', 'Edit Obat - SIKAP')
@section('page-title', 'Edit Obat / Vitamin')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Edit Obat / Vitamin</h2>
        </div>

        <form action="{{ route('obat.update', $obat) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="kode" :value="'Kode (opsional)'" />
                    <x-text-input id="kode" name="kode" class="block mt-1 w-full" :value="old('kode', $obat->kode)" />
                    <x-input-error :messages="$errors->get('kode')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="nama" :value="'Nama'" />
                    <x-text-input id="nama" name="nama" class="block mt-1 w-full" :value="old('nama', $obat->nama)" required />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="jenis" :value="'Jenis'" />
                    <select id="jenis" name="jenis" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="obat" {{ old('jenis', $obat->jenis) == 'obat' ? 'selected' : '' }}>Obat</option>
                        <option value="vitamin" {{ old('jenis', $obat->jenis) == 'vitamin' ? 'selected' : '' }}>Vitamin</option>
                    </select>
                    <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="satuan" :value="'Satuan'" />
                    <select id="satuan" name="satuan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="ml" {{ old('satuan', $obat->satuan) == 'ml' ? 'selected' : '' }}>ml</option>
                        <option value="liter" {{ old('satuan', $obat->satuan) == 'liter' ? 'selected' : '' }}>Liter</option>
                        <option value="gram" {{ old('satuan', $obat->satuan) == 'gram' ? 'selected' : '' }}>Gram</option>
                        <option value="kg" {{ old('satuan', $obat->satuan) == 'kg' ? 'selected' : '' }}>kg</option>
                        <option value="sachet" {{ old('satuan', $obat->satuan) == 'sachet' ? 'selected' : '' }}>Sachet</option>
                        <option value="botol" {{ old('satuan', $obat->satuan) == 'botol' ? 'selected' : '' }}>Botol</option>
                        <option value="butir" {{ old('satuan', $obat->satuan) == 'butir' ? 'selected' : '' }}>Butir</option>
                    </select>
                    <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="stok_minimal" :value="'Stok Minimal'" />
                    <x-text-input id="stok_minimal" name="stok_minimal" type="number" step="0.01" class="block mt-1 w-full" :value="old('stok_minimal', $obat->stok_minimal)" />
                    <x-input-error :messages="$errors->get('stok_minimal')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="status" :value="'Status'" />
                <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    <option value="aktif" {{ old('status', $obat->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', $obat->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('obat.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
