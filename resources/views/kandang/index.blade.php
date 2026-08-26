@extends('layouts.admin')

@section('title', 'Data Kandang - SIKAP')
@section('page-title', 'Data Kandang')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Kandang</h2>
            <p class="text-sm text-gray-500 mt-1">Total: {{ $kandangs->total() }} kandang</p>
        </div>
        <a href="{{ route('kandang.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kandang
        </a>
    </div>

    <form method="GET" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex gap-3">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, kode, kecamatan..." class="border-gray-300 rounded-md text-sm w-64 px-3 py-1.5">
        <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Cari</button>
        @if($search ?? false)<a href="{{ route('kandang.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200">Reset</a>@endif
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Kode</th>
                    <th class="px-6 py-3">Initial</th>
                    <th class="px-6 py-3">Nama Kandang</th>
                    <th class="px-6 py-3">Gudang</th>
                    <th class="px-6 py-3">Lokasi</th>
                    <th class="px-6 py-3">Kapasitas</th>
                    <th class="px-6 py-3">Tipe</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kandangs as $kandang)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $kandang->kode_kandang }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-bold bg-indigo-100 text-indigo-700 rounded">{{ $kandang->initial }}</span></td>
                    <td class="px-6 py-4">{{ $kandang->nama_kandang }}</td>
                    <td class="px-6 py-4">{{ $kandang->gudang->nama_gudang ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $kandang->kecamatan }}, {{ $kandang->kabupaten_kota }}</td>
                    <td class="px-6 py-4">{{ number_format($kandang->kapasitas) }} ekor</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                            {{ ucfirst(str_replace('_', ' ', $kandang->tipe_kandang)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($kandang->status == 'aktif') bg-green-100 text-green-800
                            @elseif($kandang->status == 'renovasi') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($kandang->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-1">
                            <a href="{{ route('kandang.show', $kandang) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{{ route('kandang.edit', $kandang) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('kandang.destroy', $kandang) }}" method="POST" onsubmit="return confirm('Nonaktifkan kandang ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Nonaktifkan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Belum ada data kandang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-200">
        {{ $kandangs->links() }}
    </div>
</div>
@endsection
