@extends('layouts.admin')

@section('title', 'Populasi Ayam - SIKAP')
@section('page-title', 'Populasi Ayam')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach($rekapPerKandang as $rekap)
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-medium text-gray-500">{{ $rekap->kandang->nama_kandang }}</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($rekap->kandang->populasiSekarang()) }}</p>
            <p class="text-xs text-gray-400">Populasi saat ini (ekor)</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Populasi</h2>
            <div class="flex space-x-2">
                <a href="{{ route('populasi.mutasi') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Mutasi Ayam
                </a>
                <a href="{{ route('populasi.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Input Populasi
                </a>
            </div>
        </div>

        <form method="GET" class="p-4 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600">Kandang</label>
                <select name="kandang_id" class="mt-1 border-gray-300 rounded-md text-sm w-48">
                    <option value="">Semua Kandang</option>
                    @foreach($kandangs as $k)
                        <option value="{{ $k->id }}" {{ $kandangId == $k->id ? 'selected' : '' }}>{{ $k->nama_kandang }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600">Dari</label>
                <input type="date" name="dari" value="{{ $tanggalStart }}" class="mt-1 border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600">Sampai</label>
                <input type="date" name="sampai" value="{{ $tanggalEnd }}" class="mt-1 border-gray-300 rounded-md text-sm">
            </div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Filter</button>
            <a href="{{ route('populasi.index') }}" class="px-3 py-1.5 text-gray-600 rounded-md text-sm hover:bg-gray-200">Reset</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Kandang</th>
                        <th class="px-4 py-3">Masuk</th>
                        <th class="px-4 py-3">Mati</th>
                        <th class="px-4 py-3">Afkir</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3">Input By</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($populasis as $p)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $p->kandang->nama_kandang ?? '-' }}</td>
                        <td class="px-4 py-3 text-green-600">{{ $p->jumlah_masuk > 0 ? $p->jumlah_masuk : '-' }}</td>
                        <td class="px-4 py-3 text-red-600">{{ $p->jumlah_mati > 0 ? $p->jumlah_mati : '-' }}</td>
                        <td class="px-4 py-3 text-orange-600">{{ $p->jumlah_afkir > 0 ? $p->jumlah_afkir : '-' }}</td>
                        <td class="px-4 py-3 max-w-xs truncate">{{ $p->keterangan ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $p->user->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex space-x-1">
                                <a href="{{ route('populasi.edit', $p) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('populasi.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
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
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">Belum ada data populasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $populasis->links() }}
        </div>
    </div>
</div>
@endsection
