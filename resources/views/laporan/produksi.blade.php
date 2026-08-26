@extends('layouts.admin')
@section('title', 'Laporan Produksi Telur - SIKAP')
@section('page-title', 'Laporan Produksi Telur')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-4"><p class="text-sm text-gray-500">Total Butir</p><p class="text-2xl font-bold text-gray-800">{{ number_format($summary->total_butir ?? 0) }}</p></div>
        <div class="bg-white rounded-lg shadow p-4"><p class="text-sm text-gray-500">Total Hari</p><p class="text-2xl font-bold text-gray-800">{{ number_format($data->total() ?? 0) }} record</p></div>
    </div>
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center flex-wrap gap-3"><h2 class="text-lg font-semibold text-gray-800">Data Produksi</h2><div class="flex space-x-2"><a href="{{ route('laporan.produksi.excel', request()->query()) }}" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Export Excel</a><a href="{{ route('laporan.produksi.pdf', request()->query()) }}" class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Export PDF</a></div></div>
        <form method="GET" class="p-4 border-b bg-gray-50 flex flex-wrap gap-3 items-end">
            <div><label class="block text-xs font-medium text-gray-600">Kandang</label><select name="kandang_id" class="mt-1 border-gray-300 rounded-md text-sm"><option value="">Semua</option>@foreach($kandangs as $k)<option value="{{ $k->id }}" {{ $kandangId==$k->id?'selected':'' }}>{{ $k->nama_kandang }}</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-600">Dari</label><input type="date" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <div><label class="block text-xs font-medium text-gray-600">Sampai</label><input type="date" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm">Filter</button><a href="{{ route('laporan.produksi') }}" class="px-3 py-1.5 text-gray-600 text-sm">Reset</a>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Kandang</th><th class="px-4 py-3">Butir</th><th class="px-4 py-3">Shift</th><th class="px-4 py-3">Status</th></tr></thead>
                <tbody>@forelse($data as $d)<tr class="bg-white border-b"><td class="px-4 py-2">{{ $d->tanggal->format('d/m/Y') }}</td><td class="px-4 py-2">{{ $d->kandang->nama_kandang ?? '-' }}</td><td class="px-4 py-2">{{ number_format($d->jumlah_butir) }}</td><td class="px-4 py-2">{{ $d->shift ?: '-' }}</td><td class="px-4 py-2">{{ $d->status_setor === 'sudah_disetor' ? 'Sudah Disetor' : 'Belum' }}</td></tr>@empty<tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Tidak ada data.</td></tr>@endforelse</tbody>
            </table>
        </div>
        <div class="p-4">{{ $data->links() }}</div>
    </div>
</div>
@endsection
