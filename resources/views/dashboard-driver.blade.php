@extends('layouts.admin')

@section('title', 'Dashboard Driver - SIKAP')
@section('page-title', 'Dashboard Driver')

@section('content')
<form method="GET" class="bg-white rounded-lg shadow-sm border border-gray-100 px-6 py-3 mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs text-gray-600 mb-1">Dari</label>
        <input type="date" name="dari" value="{{ $dari }}" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
    </div>
    <div>
        <label class="block text-xs text-gray-600 mb-1">Sampai</label>
        <input type="date" name="sampai" value="{{ $sampai }}" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
    </div>
    <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Filter</button>
    <a href="{{ route('dashboard') }}" class="px-4 py-1.5 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-200 rounded-md">Reset</a>
</form>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-indigo-500">Total SJ Butir</p>
        <p class="text-2xl font-bold text-indigo-700">{{ number_format($totalSJ) }} <span class="text-sm font-normal text-gray-400">butir</span></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-teal-500">Total Peti SJ</p>
        <p class="text-2xl font-bold text-teal-700">{{ number_format($totalPetiSJ) }} <span class="text-sm font-normal text-gray-400">peti</span></p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-rose-500">Total Pengeluaran</p>
        <p class="text-2xl font-bold text-rose-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 flex items-center justify-center">
        <a href="{{ route('penjualan.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Penjualan
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-800">Surat Jalan Terbaru</h3>
            <a href="{{ route('penjualan.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800">Lihat semua &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-3 py-2">No. SJ</th>
                        <th class="px-3 py-2">Tanggal</th>
                        <th class="px-3 py-2">Gudang</th>
                        <th class="px-3 py-2">Driver</th>
                        <th class="px-3 py-2 text-right">Butir</th>
                        <th class="px-3 py-2 text-right">Peti</th>
                        <th class="px-3 py-2 text-right">Berat (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sjList as $sj)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-3 py-2 font-mono text-xs font-medium text-gray-900">{{ $sj->no_referensi ?: '-' }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $sj->tanggal->format('d/m/Y') }}</td>
                        <td class="px-3 py-2">{{ $sj->gudang->nama_gudang ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $sj->driver ?: '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($sj->jumlah_butir) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($sj->peti) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($sj->berat_kg, 1) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-3 py-8 text-center text-gray-400">Belum ada surat jalan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-800">Pengeluaran Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-3 py-2">Tanggal</th>
                        <th class="px-3 py-2">Kategori</th>
                        <th class="px-3 py-2">Keterangan</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengeluaranList as $p)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-3 py-2 whitespace-nowrap">{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td class="px-3 py-2">{{ $p->kategori->nama_kategori ?? '-' }}</td>
                        <td class="px-3 py-2 truncate max-w-[120px]">{{ $p->keterangan ?: '-' }}</td>
                        <td class="px-3 py-2 text-right text-rose-600 font-medium">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-3 py-8 text-center text-gray-400">Belum ada pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
