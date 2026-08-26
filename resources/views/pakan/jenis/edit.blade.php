@extends('layouts.admin')

@section('title', 'Edit Jenis Pakan - SIKAP')
@section('page-title', 'Edit Jenis Pakan/Obat')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Edit: {{ $jenisPakan->nama }}</h2>
        </div>
        <form action="{{ route('pakan.jenis.update', $jenisPakan) }}" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <x-input-label for="nama" :value="'Nama'" />
                <x-text-input id="nama" name="nama" class="block mt-1 w-full" :value="old('nama', $jenisPakan->nama)" required />
                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="kategori" :value="'Kategori'" />
                    <select id="kategori" name="kategori" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        @foreach(['pakan', 'obat', 'vitamin'] as $k)
                            <option value="{{ $k }}" {{ old('kategori', $jenisPakan->kategori) == $k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="satuan" :value="'Satuan'" />
                    <select id="satuan" name="satuan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        @foreach(['kg','gram','liter','ml','butir','sachet','botol'] as $s)
                            <option value="{{ $s }}" {{ old('satuan', $jenisPakan->satuan) == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <x-input-label for="stok_minimal" :value="'Stok Minimal'" />
                <x-text-input id="stok_minimal" name="stok_minimal" type="number" step="0.1" class="block mt-1 w-full" :value="old('stok_minimal', $jenisPakan->stok_minimal)" />
            </div>
            <div>
                <x-input-label for="harga" :value="'Harga per Satuan (Rp)'" />
                <x-text-input id="harga" name="harga" type="text" data-type="rupiah" autocomplete="off" class="block mt-1 w-full" :value="old('harga', $jenisPakan->harga)" />
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pakan.jenis.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
