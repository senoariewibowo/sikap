@extends('layouts.admin')

@section('title', 'Edit Pengeluaran - SIKAP')
@section('page-title', 'Edit Pengeluaran')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b"><h2 class="text-lg font-semibold text-gray-800">Edit Pengeluaran</h2></div>
        <form action="{{ route('keuangan.pengeluaran.update', $pengeluaran) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><x-input-label :value="'Tanggal'" /><x-text-input name="tanggal" type="date" class="block mt-1 w-full" :value="old('tanggal', $pengeluaran->tanggal->format('Y-m-d'))" required /></div>
                <div><x-input-label :value="'Jumlah (Rp)'" /><x-text-input name="jumlah" type="text" data-type="rupiah" autocomplete="off" class="block mt-1 w-full" :value="old('jumlah', $pengeluaran->jumlah)" required min="0" /></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><x-input-label :value="'Kategori'" /><select name="kategori_pengeluaran_id" class="block mt-1 w-full border-gray-300 rounded-md text-sm">@foreach($kategoris as $k)<option value="{{ $k->id }}" {{ old('kategori_pengeluaran_id', $pengeluaran->kategori_pengeluaran_id)==$k->id?'selected':'' }}>{{ $k->nama }}</option>@endforeach</select></div>
                <div><x-input-label :value="'Kandang'" /><select name="kandang_id" class="block mt-1 w-full border-gray-300 rounded-md text-sm"><option value="">Umum</option>@foreach($kandangs as $kd)<option value="{{ $kd->id }}" {{ old('kandang_id', $pengeluaran->kandang_id)==$kd->id?'selected':'' }}>{{ $kd->nama_kandang }}</option>@endforeach</select></div>
            </div>
            <div><x-input-label :value="'Keterangan'" /><textarea name="keterangan" rows="3" class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('keterangan', $pengeluaran->keterangan) }}</textarea></div>
            <div>
                <x-input-label :value="'Bukti'" />
                @if($pengeluaran->bukti)<div class="mt-1 mb-2 text-xs text-blue-600">File: {{ basename($pengeluaran->bukti) }}</div>@endif
                <input type="file" name="bukti" accept="image/*,.pdf" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t"><a href="{{ route('keuangan.pengeluaran.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border rounded-lg">Batal</a><x-primary-button>Simpan</x-primary-button></div>
        </form>
    </div>
</div>
@endsection
