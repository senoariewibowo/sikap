@extends('layouts.admin')

@section('title', 'Stok Obat & Vitamin - SIKAP')
@section('page-title', 'Stok Obat & Vitamin')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Stok per Gudang</h2>
        <a href="{{ route('obat.stok.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Update Stok
        </a>
    </div>

    <form method="GET" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">
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
            <a href="{{ route('obat.stok.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200 inline-block ml-1">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-3 py-3">Item</th>
                    <th class="px-3 py-3">Jenis</th>
                    <th class="px-3 py-3">Gudang</th>
                    <th class="px-3 py-3 text-right">Stok Saat Ini</th>
                    <th class="px-3 py-3">Terakhir Update</th>
                    <th class="px-3 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stoks as $s)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-3 py-2 font-medium text-gray-900">{{ $s->obat->nama ?? '-' }}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 text-xs rounded-full {{ ($s->obat->jenis ?? '') === 'vitamin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($s->obat->jenis ?? '-') }}
                        </span>
                    </td>
                    <td class="px-3 py-2">{{ $s->gudang->nama_gudang ?? '-' }}</td>
                    <td class="px-3 py-2 text-right font-semibold {{ $s->obat?->stok_minimal && $s->jumlah <= $s->obat->stok_minimal ? 'text-red-600' : 'text-green-700' }}">
                        {{ number_format($s->jumlah, 2) }} {{ $s->obat->satuan ?? '' }}
                    </td>
                    <td class="px-3 py-2 text-gray-400 text-xs">
                        {{ $s->updated_at->diffForHumans() }}
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex space-x-1">
                            <a href="{{ route('obat.stok.create', ['obat_id' => $s->obat_id, 'gudang_id' => $s->gudang_id]) }}" class="px-2 py-1 text-xs bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100">Update</a>
                            <a href="{{ route('obat.stok.riwayat', $s) }}" class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded hover:bg-gray-200">Riwayat</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada stok. <a href="{{ route('obat.stok.create') }}" class="text-indigo-600 hover:underline">Tambahkan stok.</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">{{ $stoks->links() }}</div>
</div>
@endsection
