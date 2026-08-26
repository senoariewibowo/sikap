@extends('layouts.admin')

@section('title', 'Detail Produksi - SIKAP')
@section('page-title', 'Detail Produksi Telur')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Detail Produksi Telur</h2>
            <a href="{{ route('produksi.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Kembali</a>
        </div>

        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Kandang</p>
                    <p class="text-sm font-medium text-gray-900">{{ $produksi->kandang->nama_kandang ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Tanggal Produksi</p>
                    <p class="text-sm font-medium text-gray-900">{{ $produksi->tanggal->format('d M Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Shift</p>
                    <p class="text-sm font-medium text-gray-900">{{ $produksi->shift ? ucfirst($produksi->shift) : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    <p class="text-sm">
                        @if($produksi->status_setor === 'sudah_disetor')
                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">Sudah Disetor</span>
                        @else
                        <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">Belum Disetor</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Rincian Telur</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-indigo-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-indigo-600 uppercase font-medium">Karpet</p>
                        <p class="text-xl font-bold text-indigo-700">{{ number_format($produksi->karpet) }} tray</p>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-amber-600 uppercase font-medium">Sisa</p>
                        <p class="text-xl font-bold text-amber-700">{{ number_format($produksi->sisa) }} butir</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-green-600 uppercase font-medium">Total Butir</p>
                        <p class="text-xl font-bold text-green-700">{{ number_format($produksi->jumlah_butir) }}</p>
                    </div>
                </div>
            </div>
            @if($produksi->fotos->isNotEmpty())
            <div class="border-t border-gray-200 pt-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Foto Produksi ({{ $produksi->fotos->count() }})</h3>
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach($produksi->fotos as $f)
                    <img src="{{ $f->url }}" class="w-16 h-16 object-cover rounded cursor-pointer border shrink-0" onclick="openImageModal('{{ $f->url }}')">
                    @endforeach
                </div>
            </div>
            @endif
             @if($produksi->user)
            <div class="border-t border-gray-200 pt-3">
                <p class="text-xs text-gray-500">Dicatat oleh: <span class="text-gray-700 font-medium">{{ $produksi->user->name ?? '-' }}</span></p>
            </div>
            @endif
        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('produksi.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Kembali</a>
            @if($produksi->status_setor !== 'sudah_disetor' || auth()->user()->hasRole('super_admin'))
            <a href="{{ route('produksi.edit', $produksi) }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Edit</a>
            @endif
        </div>
    </div>
</div>

@include('components.image-modal')
@endsection
