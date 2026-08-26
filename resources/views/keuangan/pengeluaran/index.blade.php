@extends('layouts.admin')

@section('title', 'Pengeluaran - SIKAP')
@section('page-title', 'Pengeluaran')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total Pengeluaran</p>
            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($total, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Rata-rata / Hari</p>
            @php $days = max(1, \Carbon\Carbon::parse($dari)->diffInDays(\Carbon\Carbon::parse($sampai)) + 1) @endphp
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($total / $days, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $pengeluarans->total() }}</p>
        </div>
    </div>

    @if($perKategori->isNotEmpty())
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach($perKategori as $pk)
        <div class="bg-white rounded-lg shadow p-3">
            <p class="text-xs text-gray-500 truncate">{{ $pk->kategori->nama ?? '-' }}</p>
            <p class="text-lg font-bold text-gray-800">Rp {{ number_format($pk->total / 1000000, 1) }}M</p>
        </div>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Pengeluaran</h2>
            <div class="flex space-x-2">
                <a href="{{ route('keuangan.pengeluaran.excel', request()->query()) }}" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Excel</a>
                <a href="{{ route('keuangan.pengeluaran.pdf', request()->query()) }}" class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">PDF</a>
                <a href="{{ route('keuangan.pengeluaran.create') }}" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">&plus; Input Pengeluaran</a>
            </div>
        </div>

        <form method="GET" class="p-4 border-b bg-gray-50 flex flex-wrap gap-3 items-end">
            <div><label class="text-xs text-gray-600">Kategori</label><select name="kategori_id" class="mt-1 border-gray-300 rounded-md text-sm w-40"><option value="">Semua</option>@foreach($kategoris as $k)<option value="{{ $k->id }}" {{ $kategoriId==$k->id?'selected':'' }}>{{ $k->nama }}</option>@endforeach</select></div>
            <div><label class="text-xs text-gray-600">Kandang</label><select name="kandang_id" class="mt-1 border-gray-300 rounded-md text-sm w-36"><option value="">Semua</option>@foreach($kandangs as $kd)<option value="{{ $kd->id }}" {{ $kandangId==$kd->id?'selected':'' }}>{{ $kd->nama_kandang }}</option>@endforeach</select></div>
            <div><label class="text-xs text-gray-600">Cari</label><input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Keterangan..." class="mt-1 border-gray-300 rounded-md text-sm w-40 px-2 py-1.5"></div>
            <div><label class="text-xs text-gray-600">Dari</label><input type="date" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <div><label class="text-xs text-gray-600">Sampai</label><input type="date" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm">Filter</button>
            <a href="{{ route('keuangan.pengeluaran.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200">Reset</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Kategori</th><th class="px-4 py-3">Kandang</th><th class="px-4 py-3 text-right">Jumlah</th><th class="px-4 py-3">Keterangan</th><th class="px-4 py-3">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($pengeluarans as $p)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">{{ $p->kategori->nama ?? '-' }}</span></td>
                        <td class="px-4 py-2">{{ $p->kandang->nama_kandang ?? 'Umum' }}</td>
                        <td class="px-4 py-2 text-right font-medium text-red-600">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 max-w-xs truncate">{{ $p->keterangan ?: '-' }}</td>
                        <td class="px-4 py-2"><div class="flex space-x-1">
                            <a href="{{ route('keuangan.pengeluaran.edit', $p) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                            <form action="{{ route('keuangan.pengeluaran.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-red-600 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                        </div></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Belum ada data pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $pengeluarans->links() }}</div>
    </div>
</div>
@endsection
