@extends('layouts.admin')

@section('title', 'Grup Produk - Kasir')
@section('page-title', 'Grup Produk')

@section('content')
@php
$sortUrl = fn($column) => request()->fullUrlWithQuery(['sort' => $column, 'order' => ($sort === $column && $order === 'asc' ? 'desc' : 'asc')]);
$sortIcon = function($column) use ($sort, $order) {
    if ($sort !== $column) return '';
    return $order === 'asc'
        ? '<span class="text-emerald-500 leading-none ml-0.5">↑</span>'
        : '<span class="text-emerald-500 leading-none ml-0.5">↓</span>';
};
@endphp
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Grup Produk</h2>
        <a href="{{ route('kasir.group.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">&plus; Tambah Grup</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ $sortUrl('group_name') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-600 whitespace-nowrap">Nama Grup {!! $sortIcon('group_name') !!}</a>
                    </th>
                    <th class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ $sortUrl('description') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-600 whitespace-nowrap">Deskripsi {!! $sortIcon('description') !!}</a>
                    </th>
                    <th class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ $sortUrl('products_count') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-600 whitespace-nowrap">Jumlah Produk {!! $sortIcon('products_count') !!}</a>
                    </th>
                    <th class="px-6 py-3 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groups as $g)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $g->group_name }}</td>
                    <td class="px-6 py-3">{{ $g->description ?: '-' }}</td>
                    <td class="px-6 py-3">
                        <a href="{{ route('kasir.product.index', ['group_id' => $g->id]) }}" class="text-emerald-600 hover:text-emerald-800 hover:underline font-medium">
                            {{ $g->products_count }} produk
                        </a>
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex space-x-1">
                            <a href="{{ route('kasir.product.index', ['group_id' => $g->id]) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Lihat Produk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('kasir.group.edit', $g) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('kasir.group.destroy', $g) }}" method="POST" onsubmit="return confirm('Hapus grup ini? Produk di dalamnya jadi tanpa grup.')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-red-600 hover:bg-red-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada grup produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $groups->links() }}</div>
</div>
@endsection
