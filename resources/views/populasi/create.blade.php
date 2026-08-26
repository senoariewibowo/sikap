@extends('layouts.admin')

@section('title', 'Input Populasi - SIKAP')
@section('page-title', 'Input Populasi Ayam')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Input Populasi</h2>
        </div>
        <form action="{{ route('populasi.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <x-input-label for="kandang_id" :value="'Kandang'" />
                <select id="kandang_id" name="kandang_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                    <option value="">Pilih Kandang</option>
                    @foreach($kandangs as $k)
                        <option value="{{ $k->id }}" {{ old('kandang_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kandang }} ({{ $k->kode_kandang }}) - Populasi: {{ number_format($k->populasiSekarang()) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('kandang_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="tanggal" :value="'Tanggal'" />
                <x-text-input id="tanggal" name="tanggal" type="date" class="block mt-1 w-full" :value="old('tanggal', now()->format('Y-m-d'))" required />
                <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input-label for="jumlah_masuk" :value="'Masuk (ekor)'" />
                    <x-text-input id="jumlah_masuk" name="jumlah_masuk" type="number" class="block mt-1 w-full" :value="old('jumlah_masuk', 0)" required min="0" />
                    <x-input-error :messages="$errors->get('jumlah_masuk')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="jumlah_mati" :value="'Mati (ekor)'" />
                    <x-text-input id="jumlah_mati" name="jumlah_mati" type="number" class="block mt-1 w-full" :value="old('jumlah_mati', 0)" required min="0" />
                    <x-input-error :messages="$errors->get('jumlah_mati')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="jumlah_afkir" :value="'Afkir (ekor)'" />
                    <x-text-input id="jumlah_afkir" name="jumlah_afkir" type="number" class="block mt-1 w-full" :value="old('jumlah_afkir', 0)" required min="0" />
                    <x-input-error :messages="$errors->get('jumlah_afkir')" class="mt-2" />
                </div>
            </div>
            <div>
                <x-input-label for="keterangan" :value="'Keterangan'" />
                <textarea id="keterangan" name="keterangan" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Contoh: DOC dari supplier PT Maju, umur 16 minggu...">{{ old('keterangan') }}</textarea>
                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('populasi.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
