@extends('layouts.admin')

@section('title', 'Laporan Mortalitas - SIKAP')
@section('page-title', 'Laporan Mortalitas Ayam')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-red-500">Total Mati</p>
            <p class="text-2xl font-bold text-red-700">{{ number_format($summary->total_mati ?? 0) }} ekor</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-orange-500">Total Afkir</p>
            <p class="text-2xl font-bold text-orange-700">{{ number_format($summary->total_afkir ?? 0) }} ekor</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total Mortalitas</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format(($summary->total_mati ?? 0) + ($summary->total_afkir ?? 0)) }} ekor</p>
        </div>
    </div>

    @if($byKandang->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Per Kandang</h3>
        </div>
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr><th class="px-4 py-2">Kandang</th><th class="px-4 py-2">Mati</th><th class="px-4 py-2">Afkir</th><th class="px-4 py-2">Total</th></tr>
            </thead>
            <tbody>
                @foreach($byKandang as $bk)
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium text-gray-900">{{ $bk->kandang->nama_kandang ?? '-' }}</td>
                    <td class="px-4 py-2 text-red-600">{{ $bk->total_mati }}</td>
                    <td class="px-4 py-2 text-orange-600">{{ $bk->total_afkir }}</td>
                    <td class="px-4 py-2 font-semibold">{{ $bk->total_mati + $bk->total_afkir }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Mortalitas</h2>
            <div class="flex space-x-2">
                <a href="{{ route('laporan.mortalitas.excel', request()->query()) }}" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Export Excel</a>
                <a href="{{ route('laporan.mortalitas.pdf', request()->query()) }}" class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Export PDF</a>
            </div>
        </div>

        <form method="GET" class="p-4 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600">Kandang</label>
                <select name="kandang_id" class="mt-1 border-gray-300 rounded-md text-sm">
                    <option value="">Semua</option>
                    @foreach($kandangs as $k)
                        <option value="{{ $k->id }}" {{ $kandangId == $k->id ? 'selected' : '' }}>{{ $k->nama_kandang }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600">Dari</label>
                <input type="date" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600">Sampai</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-md text-sm">
            </div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Filter</button>
            <a href="{{ route('laporan.mortalitas') }}" class="px-3 py-1.5 text-gray-600 rounded-md text-sm hover:bg-gray-200">Reset</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Kandang</th><th class="px-4 py-3">Mati</th><th class="px-4 py-3">Afkir</th><th class="px-4 py-3">Keterangan</th></tr>
                </thead>
                <tbody>
                    @forelse($data as $d)
                    <tr class="bg-white border-b">
                        <td class="px-4 py-2">{{ $d->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $d->kandang->nama_kandang ?? '-' }}</td>
                        <td class="px-4 py-2 text-red-600">{{ $d->jumlah_mati > 0 ? $d->jumlah_mati : '-' }}</td>
                        <td class="px-4 py-2 text-orange-600">{{ $d->jumlah_afkir > 0 ? $d->jumlah_afkir : '-' }}</td>
                        <td class="px-4 py-2">{{ $d->keterangan ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Tidak ada data mortalitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $data->links() }}</div>
    </div>
</div>
@endsection
