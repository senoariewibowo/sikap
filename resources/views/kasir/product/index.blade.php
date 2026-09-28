@extends('layouts.admin')

@section('title', 'Produk - Kasir')
@section('page-title', 'Master Produk Kasir')

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
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Produk</h2>
            @if($groupId)
                @php $activeGroup = $groups->firstWhere('id', $groupId); @endphp
                @if($activeGroup)
                <p class="text-sm text-emerald-600 mt-1">Filter grup: <span class="font-semibold">{{ $activeGroup->group_name }}</span></p>
                @endif
            @endif
        </div>
        <a href="{{ route('kasir.product.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">&plus; Tambah Produk</a>
    </div>

    <form method="GET" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500">Cari</label>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nama / barcode..." class="border-gray-300 rounded-md text-sm w-64 px-3 py-1.5">
        </div>
        <div>
            <label class="block text-xs text-gray-500">Grup</label>
            <select name="group_id" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
                <option value="">Semua Grup</option>
                @foreach($groups as $g)
                <option value="{{ $g->id }}" {{ $groupId == $g->id ? 'selected' : '' }}>{{ $g->group_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Cari</button>
        @if($search || $groupId)<a href="{{ route('kasir.product.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200 rounded-md">Reset</a>@endif
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ $sortUrl('product_name') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-600 whitespace-nowrap">Nama {!! $sortIcon('product_name') !!}</a>
                    </th>
                    <th class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ $sortUrl('barcode') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-600 whitespace-nowrap">Barcode {!! $sortIcon('barcode') !!}</a>
                    </th>
                    <th class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ $sortUrl('group_name') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-600 whitespace-nowrap">Grup {!! $sortIcon('group_name') !!}</a>
                    </th>
                    <th class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ $sortUrl('modal') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-600 whitespace-nowrap">Modal {!! $sortIcon('modal') !!}</a>
                    </th>
                    <th class="px-6 py-3 whitespace-nowrap">Harga Tier</th>
                    <th class="px-6 py-3 whitespace-nowrap">
                        <a href="{{ $sortUrl('is_active') }}" class="inline-flex items-center gap-1.5 hover:text-emerald-600 whitespace-nowrap">Status {!! $sortIcon('is_active') !!}</a>
                    </th>
                    <th class="px-6 py-3 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $p->product_name }}</td>
                    <td class="px-6 py-3">{{ $p->barcode ?: '-' }}</td>
                    <td class="px-6 py-3">{{ $p->group->group_name ?? '-' }}</td>
                    <td class="px-6 py-3">Rp {{ number_format($p->modal ?? 0, 0, ',', '.') }}</td>
                    <td class="px-6 py-3 text-xs">
                        @foreach($p->prices as $pr)
                        <div>≥{{ $pr->min_qty }}: <span class="font-semibold text-gray-700">Rp {{ number_format($pr->harga, 0, ',', '.') }}</span></div>
                        @endforeach
                    </td>
                    <td class="px-6 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $p->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex space-x-1">
                            <a href="{{ route('kasir.product.edit', $p) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('kasir.product.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-red-600 hover:bg-red-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $products->links() }}</div>
</div>
@endsection
