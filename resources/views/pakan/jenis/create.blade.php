@extends('layouts.admin')
@section('title', 'Tambah Jenis - SIKAP')
@section('page-title', 'Tambah Jenis')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b"><h2 class="text-lg font-semibold text-gray-800">Form Tambah Jenis</h2></div>
        <form action="{{ route('pakan.jenis.store') }}" method="POST" class="p-6 space-y-4">@csrf
            <div><x-input-label :value="'Nama'" /><x-text-input name="nama" class="block mt-1 w-full" :value="old('nama')" placeholder="Contoh: Pakan Layer 105, Vitachick, dll." required /></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="'Kategori'" />
                    @php $kat = old('kategori', $defaultKategori ?? 'pakan') @endphp
                    <select name="kategori" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                        <option value="pakan" {{ $kat == 'pakan' ? 'selected' : '' }}>Pakan</option>
                        <option value="obat" {{ $kat == 'obat' ? 'selected' : '' }}>Obat</option>
                        <option value="vitamin" {{ $kat == 'vitamin' ? 'selected' : '' }}>Vitamin</option>
                    </select>
                </div>
                <div><x-input-label :value="'Satuan'" /><select name="satuan" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>@foreach(['kg','gram','liter','ml','butir','sachet','botol'] as $s)<option value="{{ $s }}" {{ old('satuan')==$s?'selected':'' }}>{{ $s }}</option>@endforeach</select></div>
            </div>
            <div><x-input-label :value="'Stok Minimal'" /><x-text-input name="stok_minimal" type="number" step="0.1" class="block mt-1 w-full" :value="old('stok_minimal')" /></div>
            <div><x-input-label :value="'Harga per Satuan'" /><x-text-input name="harga" type="text" data-type="rupiah" autocomplete="off" class="block mt-1 w-full" :value="old('harga')" /><p class="text-xs text-gray-400 mt-1">Untuk menghitung nilai inventori.</p></div>
            <div class="flex justify-end space-x-3 pt-4 border-t"><a href="{{ route('pakan.jenis.index', ['kategori' => $kat]) }}" class="px-4 py-2 text-sm text-gray-700 bg-white border rounded-lg hover:bg-gray-50">Batal</a><x-primary-button>Simpan</x-primary-button></div>
        </form>
    </div>
</div>
@endsection
