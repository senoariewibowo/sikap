@extends('layouts.admin')

@section('title', 'Tambah Kandang - SIKAP')
@section('page-title', 'Tambah Kandang')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Tambah Kandang</h2>
        </div>

        <form action="{{ route('kandang.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="kode_kandang" :value="'Kode Kandang (otomatis)'" />
                    <x-text-input id="kode_kandang" name="kode_kandang" class="block mt-1 w-full bg-gray-100 cursor-not-allowed" :value="old('kode_kandang', $nextKode ?? '')" readonly required />
                    <x-input-error :messages="$errors->get('kode_kandang')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="gudang_id" :value="'Gudang'" />
                    <select id="gudang_id" name="gudang_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
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
                    <x-input-label for="nama_kandang" :value="'Nama Kandang'" />
                    <x-text-input id="nama_kandang" name="nama_kandang" class="block mt-1 w-full" :value="old('nama_kandang')" required />
                    <x-input-error :messages="$errors->get('nama_kandang')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="alamat_jalan" :value="'Alamat Jalan'" />
                <x-text-input id="alamat_jalan" name="alamat_jalan" class="block mt-1 w-full" :value="old('alamat_jalan')" required />
                <x-input-error :messages="$errors->get('alamat_jalan')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="desa_kelurahan" :value="'Desa/Kelurahan'" />
                    <x-text-input id="desa_kelurahan" name="desa_kelurahan" class="block mt-1 w-full" :value="old('desa_kelurahan')" required />
                    <x-input-error :messages="$errors->get('desa_kelurahan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="kecamatan" :value="'Kecamatan'" />
                    <x-text-input id="kecamatan" name="kecamatan" class="block mt-1 w-full" :value="old('kecamatan')" required />
                    <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="kabupaten_kota" :value="'Kabupaten/Kota'" />
                    <x-text-input id="kabupaten_kota" name="kabupaten_kota" class="block mt-1 w-full" :value="old('kabupaten_kota')" required />
                    <x-input-error :messages="$errors->get('kabupaten_kota')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="provinsi" :value="'Provinsi'" />
                    <x-text-input id="provinsi" name="provinsi" class="block mt-1 w-full" :value="old('provinsi')" required />
                    <x-input-error :messages="$errors->get('provinsi')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="kode_pos" :value="'Kode Pos'" />
                    <x-text-input id="kode_pos" name="kode_pos" class="block mt-1 w-full" :value="old('kode_pos')" required />
                    <x-input-error :messages="$errors->get('kode_pos')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="kapasitas" :value="'Kapasitas (ekor)'" />
                    <x-text-input id="kapasitas" name="kapasitas" type="number" class="block mt-1 w-full" :value="old('kapasitas')" required min="1" />
                    <x-input-error :messages="$errors->get('kapasitas')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tipe_kandang" :value="'Tipe Kandang'" />
                    <select id="tipe_kandang" name="tipe_kandang" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Tipe</option>
                        <option value="baterai" {{ old('tipe_kandang') == 'baterai' ? 'selected' : '' }}>Baterai</option>
                        <option value="postal" {{ old('tipe_kandang') == 'postal' ? 'selected' : '' }}>Postal</option>
                        <option value="closed_house" {{ old('tipe_kandang') == 'closed_house' ? 'selected' : '' }}>Closed House</option>
                    </select>
                    <x-input-error :messages="$errors->get('tipe_kandang')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="status" :value="'Status'" />
                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                        <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="renovasi" {{ old('status') == 'renovasi' ? 'selected' : '' }}>Renovasi</option>
                        <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="latitude" :value="'Latitude (opsional)'" />
                    <x-text-input id="latitude" name="latitude" type="number" step="any" class="block mt-1 w-full" :value="old('latitude')" />
                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="longitude" :value="'Longitude (opsional)'" />
                    <x-text-input id="longitude" name="longitude" type="number" step="any" class="block mt-1 w-full" :value="old('longitude')" />
                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="foto" :value="'Foto Kandang (opsional, max 2MB)'" />
                <input id="foto" name="foto" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <x-input-error :messages="$errors->get('foto')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('kandang.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
