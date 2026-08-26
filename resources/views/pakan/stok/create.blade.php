@extends('layouts.admin')

@section('title', 'Update Stok Pakan - SIKAP')
@section('page-title', 'Update Stok Pakan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Update Stok Pakan</h2>
            <p class="text-sm text-gray-500 mt-1">Tambahkan stok pakan yang baru datang ke gudang.</p>
        </div>

        <form action="{{ route('pakan.stok.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="pakan_id" :value="'Pakan'" />
                    <select id="pakan_id" name="pakan_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Pakan</option>
                        @foreach($pakan as $p)
                        <option value="{{ $p->id }}" {{ old('pakan_id', request('pakan_id')) == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->satuan }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('pakan_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="gudang_id" :value="'Gudang'" />
                    <select id="gudang_id" name="gudang_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Gudang</option>
                        @foreach($gudangs as $g)
                        <option value="{{ $g->id }}" {{ old('gudang_id', request('gudang_id')) == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('gudang_id')" class="mt-2" />
                </div>
            </div>

            @php
                $currentStok = false;
                if (request('pakan_id') && request('gudang_id')) {
                    $currentStok = \App\Models\PakanStok::where('pakan_id', request('pakan_id'))
                        ->where('gudang_id', request('gudang_id'))->first();
                }
            @endphp

            @if($currentStok)
            <div class="bg-blue-50 rounded-lg p-3 text-sm">
                Stok saat ini: <strong>{{ number_format($currentStok->jumlah, 1) }} {{ $currentStok->pakan->satuan }}</strong>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="jumlah" :value="'Tambah Jumlah'" />
                    <x-text-input id="jumlah" name="jumlah" type="number" step="0.01" class="block mt-1 w-full" :value="old('jumlah')" required />
                    <p class="text-xs text-gray-400 mt-1">Jumlah yang baru datang (bukan total).</p>
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
                <textarea id="keterangan" name="keterangan" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('keterangan') }}</textarea>
                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pakan.stok.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
