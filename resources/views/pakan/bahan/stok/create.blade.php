@extends('layouts.admin')

@section('title', 'Tambah Stok Bahan Pakan - SIKAP')
@section('page-title', 'Tambah Stok Bahan Pakan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Tambah Stok Bahan Pakan</h2>
        </div>

        <form action="{{ route('pakan.bahan.stok.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="bahan_pakan_id" :value="'Bahan Pakan'" />
                    <select id="bahan_pakan_id" name="bahan_pakan_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Bahan</option>
                        @foreach($bahans as $b)
                        <option value="{{ $b->id }}" {{ old('bahan_pakan_id') == $b->id ? 'selected' : '' }}>{{ $b->nama }} ({{ $b->satuan }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('bahan_pakan_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="gudang_id" :value="'Gudang'" />
                    <select id="gudang_id" name="gudang_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Gudang</option>
                        @foreach($gudangs as $g)
                        <option value="{{ $g->id }}" {{ old('gudang_id') == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('gudang_id')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="jumlah" :value="'Jumlah'" />
                    <x-text-input id="jumlah" name="jumlah" type="number" step="0.01" class="block mt-1 w-full" :value="old('jumlah')" required />
                    <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tanggal" :value="'Tanggal'" />
                    <x-text-input id="tanggal" name="tanggal" type="date" class="block mt-1 w-full" :value="old('tanggal', date('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="keterangan" :value="'Keterangan (opsional)'" />
                <x-text-input id="keterangan" name="keterangan" class="block mt-1 w-full" :value="old('keterangan')" />
                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pakan.bahan.stok.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
