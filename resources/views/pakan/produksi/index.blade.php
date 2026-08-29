@extends('layouts.admin')

@section('title', 'Produksi Pakan - SIKAP')
@section('page-title', 'Produksi Pakan')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Produksi Pakan</h2>
            <p class="text-sm text-gray-500 mt-1">Total: {{ $produksis->total() }} data</p>
        </div>
        <a href="{{ route('pakan.produksi.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Produksi
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
            <a href="{{ route('pakan.produksi.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200 inline-block ml-1">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-3 py-3">Tanggal</th>
                    <th class="px-3 py-3">Pakan Hasil</th>
                    <th class="px-3 py-3">Gudang</th>
                    <th class="px-3 py-3 text-right">Jumlah</th>
                    <th class="px-3 py-3 text-right">HPP Total</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produksis as $p)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $p->tanggal->format('d M Y') }}</td>
                    <td class="px-3 py-2 font-medium text-gray-900">{{ $p->pakan->nama ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $p->gudang->nama_gudang ?? '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($p->jumlah, 2) }} {{ $p->pakan->satuan ?? '' }}</td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($p->hpp_total, 0, ',', '.') }}</td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 text-xs rounded-full {{ $p->status === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex space-x-1">
                            <a href="{{ route('pakan.produksi.show', $p) }}" class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <form action="{{ route('pakan.produksi.destroy', $p) }}" method="POST" onsubmit="return confirm('Batalkan produksi ini? Stok bahan akan kembali dan stok pakan hasil akan berkurang.')">
                                @csrf @method('DELETE')
                                <button class="p-1 text-red-600 hover:bg-red-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada data produksi pakan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">{{ $produksis->links() }}</div>
</div>
@endsection
