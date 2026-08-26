@extends('layouts.admin')
@section('title', 'Edit Customer - SIKAP')
@section('page-title', 'Edit Customer')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b"><h2 class="text-lg font-semibold text-gray-800">Edit: {{ $customer->nama_customer }}</h2></div>
        <form action="{{ route('customer.update', $customer) }}" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><x-input-label :value="'Nama'" /><x-text-input name="nama_customer" class="block mt-1 w-full" :value="old('nama_customer', $customer->nama_customer)" required /></div>
                <div><x-input-label :value="'Tipe'" /><select name="tipe_customer" class="block mt-1 w-full border-gray-300 rounded-md text-sm">@foreach(['agen','pengepul','retail','korporat'] as $t)<option value="{{ $t }}" {{ old('tipe_customer',$customer->tipe_customer)==$t?'selected':'' }}>{{ ucfirst($t) }}</option>@endforeach</select></div>
            </div>
            <div><x-input-label :value="'Alamat'" /><x-text-input name="alamat" class="block mt-1 w-full" :value="old('alamat', $customer->alamat)" /></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><x-input-label :value="'No. HP'" /><x-text-input name="no_hp" class="block mt-1 w-full" :value="old('no_hp', $customer->no_hp)" /></div>
                <div><x-input-label :value="'Kontak Person'" /><x-text-input name="kontak_person" class="block mt-1 w-full" :value="old('kontak_person', $customer->kontak_person)" /></div>
            </div>
            <div><x-input-label :value="'Status'" /><select name="status" class="block mt-1 w-full border-gray-300 rounded-md text-sm"><option value="aktif" {{ old('status',$customer->status)=='aktif'?'selected':'' }}>Aktif</option><option value="nonaktif" {{ old('status',$customer->status)=='nonaktif'?'selected':'' }}>Nonaktif</option></select></div>
            <div class="flex justify-end space-x-3 pt-4 border-t"><a href="{{ route('customer.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border rounded-lg">Batal</a><x-primary-button>Simpan</x-primary-button></div>
        </form>
    </div>
</div>
@endsection
