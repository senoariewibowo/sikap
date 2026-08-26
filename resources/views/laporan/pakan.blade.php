@extends('layouts.admin')

@section('title', 'Laporan Pakan - SIKAP')
@section('page-title', 'Laporan Penggunaan Pakan & Obat')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-green-500">Total Masuk</p>
            <p class="text-2xl font-bold text-green-700">{{ number_format($summaryMasuk, 1) }} kg</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-orange-500">Total Keluar</p>
            <p class="text-2xl font-bold text-orange-700">{{ number_format($summaryKeluar, 1) }} kg</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Selisih</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($summaryMasuk - $summaryKeluar, 1) }} kg</p>
        </div>
    </div>

    @if($byJenis->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-700">Per Jenis</h3></div>
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr><th class="px-4 py-2">Jenis</th><th class="px-4 py-2">Tipe</th><th class="px-4 py-2">Total</th></tr>
            </thead>
            <tbody>
                @foreach($byJenis as $bj)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $bj->jenisPakan->nama ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $bj->tipe == 'masuk' ? 'Masuk' : 'Keluar' }}</td>
                    <td class="px-4 py-2">{{ number_format($bj->total, 1) }} {{ $bj->jenisPakan->satuan ?? 'kg' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Transaksi</h2>
            <div class="flex space-x-2">
                <a href="{{ route('laporan.pakan.excel', request()->query()) }}" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Export Excel</a>
                <a href="{{ route('laporan.pakan.pdf', request()->query()) }}" class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Export PDF</a>
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
            <a href="{{ route('laporan.pakan') }}" class="px-3 py-1.5 text-gray-600 rounded-md text-sm hover:bg-gray-200">Reset</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Tipe</th><th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Kandang</th></tr>
                </thead>
                <tbody>
                    @forelse($data as $d)
                    <tr class="bg-white border-b">
                        <td class="px-4 py-2">{{ $d->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $d->jenisPakan->nama ?? '-' }}</td>
                        <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded-full {{ $d->tipe=='masuk' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">{{ $d->tipe=='masuk'?'Masuk':'Keluar' }}</span></td>
                        <td class="px-4 py-2">{{ number_format($d->jumlah_kg, 1) }} {{ $d->jenisPakan->satuan ?? 'kg' }}</td>
                        <td class="px-4 py-2">{{ $d->kandang->nama_kandang ?? 'Gudang Pusat' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $data->links() }}</div>
    </div>
</div>
@endsection
