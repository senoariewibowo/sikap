@extends('layouts.admin')
@section('title','Edit Kategori - SIKAP')
@section('page-title','Edit Kategori Pengeluaran')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b"><h2 class="text-lg font-semibold text-gray-800">Edit: {{ $kategori->nama }}</h2></div>
        <form action="{{ route('keuangan.kategori.update', $kategori) }}" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div><x-input-label :value="'Nama Kategori'" /><x-text-input name="nama" class="block mt-1 w-full" :value="old('nama', $kategori->nama)" required /></div>
            <div class="flex justify-end space-x-3 pt-4 border-t"><a href="{{ route('keuangan.kategori.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border rounded-lg">Batal</a><x-primary-button>Simpan</x-primary-button></div>
        </form>
    </div>
</div>
@endsection
