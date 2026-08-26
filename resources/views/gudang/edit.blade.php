@extends('layouts.admin')

@section('title', 'Edit Gudang - SIKAP')
@section('page-title', 'Edit Gudang')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Edit Gudang: {{ $gudang->nama_gudang }}</h2>
        </div>

        <form action="{{ route('gudang.update', $gudang) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label :value="'Kode Gudang'" />
                <x-text-input class="block mt-1 w-full bg-gray-100 cursor-not-allowed" :value="$gudang->kode_gudang" readonly disabled />
            </div>

            <div>
                <x-input-label for="nama_gudang" :value="'Nama Gudang'" />
                <x-text-input id="nama_gudang" name="nama_gudang" class="block mt-1 w-full" :value="old('nama_gudang', $gudang->nama_gudang)" required />
                <x-input-error :messages="$errors->get('nama_gudang')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="lokasi" :value="'Lokasi'" />
                <x-text-input id="lokasi" name="lokasi" class="block mt-1 w-full" :value="old('lokasi', $gudang->lokasi)" />
                <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="status" :value="'Status'" />
                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                    <option value="aktif" {{ old('status', $gudang->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', $gudang->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('gudang.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
