@extends('layouts.admin')

@section('title', 'Mutasi Ayam - SIKAP')
@section('page-title', 'Mutasi Ayam Antar Kandang')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 bg-yellow-50">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-yellow-700">Mutasi akan mencatat pengurangan di kandang asal (afkir) dan penambahan di kandang tujuan (masuk) secara otomatis.</p>
            </div>
        </div>
        <form method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="kandang_asal_id" :value="'Kandang Asal'" />
                    <select id="kandang_asal_id" name="kandang_asal_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Kandang Asal</option>
                        @foreach($kandangs as $k)
                            <option value="{{ $k->id }}" {{ old('kandang_asal_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kandang }} (Populasi: {{ number_format($k->populasiSekarang()) }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('kandang_asal_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="kandang_tujuan_id" :value="'Kandang Tujuan'" />
                    <select id="kandang_tujuan_id" name="kandang_tujuan_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Kandang Tujuan</option>
                        @foreach($kandangs as $k)
                            <option value="{{ $k->id }}" {{ old('kandang_tujuan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kandang }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('kandang_tujuan_id')" class="mt-2" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="jumlah" :value="'Jumlah (ekor)'" />
                    <x-text-input id="jumlah" name="jumlah" type="number" class="block mt-1 w-full" :value="old('jumlah')" required min="1" />
                    <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tanggal" :value="'Tanggal'" />
                    <x-text-input id="tanggal" name="tanggal" type="date" class="block mt-1 w-full" :value="old('tanggal', now()->format('Y-m-d'))" required />
                </div>
            </div>
            <div>
                <x-input-label for="keterangan" :value="'Keterangan'" />
                <textarea id="keterangan" name="keterangan" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Alasan mutasi...">{{ old('keterangan') }}</textarea>
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('populasi.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Proses Mutasi</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
