@extends('layouts.admin')
@section('title', 'Stok Gudang - SIKAP')
@section('page-title', 'Stok Telur Gudang')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4"><p class="text-sm text-gray-500">Total Sortir</p><p class="text-2xl font-bold text-gray-800">{{ number_format($perGudang->sum('masuk')) }} butir</p></div>
        <div class="bg-white rounded-lg shadow p-4"><p class="text-sm text-gray-500">Keluar Customer</p><p class="text-2xl font-bold text-blue-600">{{ number_format($perGudang->sum('keluar_customer')) }} butir</p></div>
        <div class="bg-white rounded-lg shadow p-4"><p class="text-sm text-gray-500">Alokasi Eceran</p><p class="text-2xl font-bold text-amber-600">{{ number_format($perGudang->sum('keluar_eceran')) }} butir</p></div>
        <div class="bg-white rounded-lg shadow p-4"><p class="text-sm text-gray-500">Sisa Stok</p><p class="text-2xl font-bold text-green-600">{{ number_format($perGudang->sum('sisa')) }} butir</p></div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b"><h2 class="text-lg font-semibold text-gray-800">Stok Per Gudang</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr><th class="px-4 py-3">Gudang</th><th class="px-4 py-3 text-right">Peti</th><th class="px-4 py-3 text-right">Masuk</th><th class="px-4 py-3 text-right">Customer</th><th class="px-4 py-3 text-right">Eceran</th><th class="px-4 py-3 text-right">Sisa</th><th class="px-4 py-3">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($perGudang as $r)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $r->gudang->nama_gudang }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($r->peti) }}</td>
                        <td class="px-4 py-2 text-right text-indigo-600">{{ number_format($r->masuk) }}</td>
                        <td class="px-4 py-2 text-right text-blue-600">{{ number_format($r->keluar_customer) }}</td>
                        <td class="px-4 py-2 text-right text-amber-600">{{ number_format($r->keluar_eceran) }}</td>
                        <td class="px-4 py-2 text-right font-semibold {{ $r->sisa >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($r->sisa) }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('setoran.gudang.detail', $r->gudang) }}" class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Belum ada stok di gudang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
