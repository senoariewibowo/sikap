@extends('layouts.admin')
@section('title', 'Jenis ' . ($kategoriLabel ?? 'Pakan') . ' - SIKAP')
@section('page-title', 'Jenis ' . ($kategoriLabel ?? 'Pakan'))

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Jenis {{ $kategoriLabel ?? 'Pakan' }}</h2>
            <p class="text-sm text-gray-500 mt-1">Total: {{ $jenisPakans->total() }} item</p>
        </div>
        <a href="{{ route('pakan.jenis.create', ['kategori' => $kategori]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Tambah</a>
    </div>

    <form method="GET" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex gap-3">
        <input type="hidden" name="kategori" value="{{ $kategori }}">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama..." class="border-gray-300 rounded-md text-sm w-64 px-3 py-1.5">
        <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Cari</button>
        @if($search ?? false)<a href="{{ route('pakan.jenis.index', ['kategori' => $kategori]) }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200">Reset</a>@endif
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Satuan</th>
                    <th class="px-6 py-3">Stok Minimal</th>
                    <th class="px-6 py-3">Harga/Satuan</th>
                    <th class="px-6 py-3">Stok</th>
                    <th class="px-6 py-3">Nilai Stok</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jenisPakans as $jp)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $jp->nama }}</td>
                    <td class="px-6 py-4">{{ $jp->satuan }}</td>
                    <td class="px-6 py-4">{{ $jp->stok_minimal ? rtrim(rtrim(number_format($jp->stok_minimal, 1, ',', '.'), '0'), ',') . ' ' . $jp->satuan : '-' }}</td>
                    <td class="px-6 py-4 text-right font-medium">{{ $jp->harga ? 'Rp ' . number_format($jp->harga, 0, ',', '.') : '-' }}</td>
                    <td class="px-6 py-4 font-medium text-right">@jumlah($jp->stokSekarang()) {{ $jp->satuan }}</td>
                    <td class="px-6 py-4 text-right font-semibold {{ $jp->nilaiStok() > 0 ? 'text-gray-800' : 'text-gray-400' }}">{{ $jp->nilaiStok() > 0 ? 'Rp ' . number_format($jp->nilaiStok(), 0, ',', '.') : '-' }}</td>
                    <td class="px-6 py-4">
                        @if($jp->isStokMenipis())
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Menipis</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aman</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-1">
                            <a href="{{ route('pakan.jenis.edit', $jp) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('pakan.jenis.destroy', $jp) }}" method="POST" onsubmit="return confirm('Hapus jenis ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Hapus">
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
                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">Belum ada data jenis pakan/obat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-200">
        {{ $jenisPakans->links() }}
    </div>
</div>
@endsection
