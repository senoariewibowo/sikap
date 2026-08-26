@extends('layouts.admin')
@section('title','Edit Harga - SIKAP')
@section('page-title','Edit Harga Telur')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b"><h2 class="text-lg font-semibold text-gray-800">Edit Harga</h2></div>
        <form action="{{ route('harga.update', $harga) }}" method="POST" class="p-6 space-y-4">@csrf @method('PUT')
            <div>
                <x-input-label :value="'Harga (Rp)'" />
                <x-text-input name="harga" type="text" data-type="rupiah" autocomplete="off" class="block mt-1 w-full" :value="old('harga', $harga->harga)" required />
                <x-input-error :messages="$errors->get('harga')" class="mt-1" />
            </div>
            <div>
                <x-input-label :value="'Satuan'" />
                <select name="satuan" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                    <option value="per_butir" {{ old('satuan',$harga->satuan)=='per_butir'?'selected':'' }}>Per Butir</option>
                    <option value="per_kg" {{ old('satuan',$harga->satuan)=='per_kg'?'selected':'' }}>Per Kg</option>
                    <option value="per_karpet" {{ old('satuan',$harga->satuan)=='per_karpet'?'selected':'' }}>Per Karpet (30 butir)</option>
                    <option value="per_peti" {{ old('satuan',$harga->satuan)=='per_peti'?'selected':'' }}>Per Peti</option>
                </select>
                <x-input-error :messages="$errors->get('satuan')" class="mt-1" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label :value="'Customer'" />
                    <select name="customer_id" class="block mt-1 w-full border-gray-300 rounded-md text-sm">
                        <option value="">Umum</option>
                        @foreach($customers as $c)<option value="{{ $c->id }}" {{ old('customer_id',$harga->customer_id)==$c->id?'selected':'' }}>{{ $c->nama_customer }}</option>@endforeach
                    </select>
                    <x-input-error :messages="$errors->get('customer_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label :value="'Tgl Berlaku'" />
                    <x-text-input name="tanggal_mulai_berlaku" type="date" class="block mt-1 w-full" :value="old('tanggal_mulai_berlaku', $harga->tanggal_mulai_berlaku->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('tanggal_mulai_berlaku')" class="mt-1" />
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4 border-t"><a href="{{ route('harga.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border rounded-lg">Batal</a><x-primary-button>Simpan</x-primary-button></div>
        </form>
    </div>
</div>
@endsection
