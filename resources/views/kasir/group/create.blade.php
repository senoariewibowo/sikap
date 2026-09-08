@extends('layouts.admin')

@section('title', 'Tambah Grup - Kasir')
@section('page-title', 'Tambah Grup Produk')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('kasir.group.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <x-input-label :value="'Nama Grup'" />
                    <input type="text" name="group_name" value="{{ old('group_name') }}" class="block mt-1 w-full border-gray-300 rounded-md text-sm" required>
                    @error('group_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <x-input-label :value="'Deskripsi'" />
                    <textarea name="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md text-sm">{{ old('description') }}</textarea>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 font-semibold">Simpan</button>
                    <a href="{{ route('kasir.group.index') }}" class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
