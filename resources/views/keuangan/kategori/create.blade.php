@extends('layouts.admin')
@section('title','Tambah Kategori - SIKAP')
@section('page-title','Tambah Kategori Pengeluaran')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b"><h2 class="text-lg font-semibold text-gray-800">Form Tambah Kategori</h2></div>
        <form action="{{ route('keuangan.kategori.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div><x-input-label :value="'Nama Kategori'" /><x-text-input name="nama" class="block mt-1 w-full" :value="old('nama')" required /></div>
            <div class="flex justify-end space-x-3 pt-4 border-t"><a href="{{ route('keuangan.kategori.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border rounded-lg hover:bg-gray-50">Batal</a><x-primary-button>Simpan</x-primary-button></div>
        </form>
    </div>
</div>
@endsection
