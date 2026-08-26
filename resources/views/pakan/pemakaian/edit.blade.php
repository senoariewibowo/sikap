@extends('layouts.admin')

@section('title', 'Edit Pemakaian Pakan - SIKAP')
@section('page-title', 'Edit Pemakaian Pakan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Form Edit Pemakaian Pakan</h2>
        </div>

        <form action="{{ route('pakan.pemakaian.update', $pemakaian) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" name="kandang_id" value="{{ old('kandang_id', $pemakaian->kandang_id) }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="pakan_id" :value="'Pakan'" />
                    <select id="pakan_id" name="pakan_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                        <option value="">Pilih Pakan</option>
                        @foreach($pakans as $p)
                        <option value="{{ $p->id }}" {{ old('pakan_id', $pemakaian->pakan_id) == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->satuan }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('pakan_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="jumlah" :value="'Jumlah'" />
                    <x-text-input id="jumlah" name="jumlah" type="number" step="0.01" class="block mt-1 w-full" :value="old('jumlah', $pemakaian->jumlah)" required />
                    <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="tanggal" :value="'Tanggal'" />
                    <x-text-input id="tanggal" name="tanggal" type="date" class="block mt-1 w-full" :value="old('tanggal', $pemakaian->tanggal->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="keterangan" :value="'Keterangan (opsional)'" />
                <textarea id="keterangan" name="keterangan" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('keterangan', $pemakaian->keterangan) }}</textarea>
                <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('pakan.pemakaian.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
