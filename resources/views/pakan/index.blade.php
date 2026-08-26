@extends('layouts.admin')

@section('title', 'Master Pakan - SIKAP')
@section('page-title', 'Master Pakan')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Pakan</h2>
            <p class="text-sm text-gray-500 mt-1">Total: {{ $pakans->total() }} jenis pakan</p>
        </div>
        <a href="{{ route('pakan.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pakan
        </a>
    </div>

    <form method="GET" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-600 mb-1">Cari</label>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nama atau kode..." class="border-gray-300 rounded-md text-sm px-3 py-1.5">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Gudang</label>
            <select name="gudang_id" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
                <option value="">Semua Gudang</option>
                @foreach($gudangs as $g)
                <option value="{{ $g->id }}" {{ ($gudangId ?? '') == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Filter</button>
            <a href="{{ route('pakan.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200 inline-block ml-1">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-3 py-3">Kode</th>
                    <th class="px-3 py-3">Nama</th>
                    <th class="px-3 py-3">Satuan</th>
                    <th class="px-3 py-3 text-right">Harga</th>
                    <th class="px-3 py-3 text-right">Stok Min</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pakans as $p)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-3 py-2 font-mono text-xs text-gray-500">{{ $p->kode ?: '-' }}</td>
                    <td class="px-3 py-2 font-medium text-gray-900">{{ $p->nama }}</td>
                    <td class="px-3 py-2">{{ $p->satuan }}</td>
                    <td class="px-3 py-2 text-right">{{ $p->harga ? 'Rp ' . number_format($p->harga, 0, ',', '.') : '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ $p->stok_minimal ? number_format($p->stok_minimal, 1) : '-' }}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 text-xs rounded-full {{ $p->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex space-x-1">
                            <a href="{{ route('pakan.edit', $p) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('pakan.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus pakan ini?')">
                                @csrf @method('DELETE')
                                <button class="p-1 text-red-600 hover:bg-red-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada data pakan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">{{ $pakans->links() }}</div>
</div>
@endsection
