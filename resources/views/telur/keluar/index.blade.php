@extends('layouts.admin')
@section('title', 'Telur Keluar - SIKAP')
@section('page-title', 'Telur Keluar')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4"><p class="text-sm text-orange-500">Total Keluar Butir</p><p class="text-2xl font-bold text-orange-700">{{ number_format($keluarButirSJ) }} butir</p></div>
        <div class="bg-white rounded-lg shadow p-4"><p class="text-sm text-teal-600">Total Keluar Peti</p><p class="text-2xl font-bold text-teal-700">{{ number_format($keluarPetiSJ) }} peti</p></div>
        <div class="bg-white rounded-lg shadow p-4 border border-emerald-200"><p class="text-sm text-emerald-600">Sisa Stok</p><p class="text-2xl font-bold text-emerald-700">{{ number_format(max(0, $masukAll - $keluarAll)) }} butir</p></div>
        <div class="bg-white rounded-lg shadow p-4 border border-rose-200"><p class="text-sm text-rose-600">Sisa Peti</p><p class="text-2xl font-bold text-rose-700">{{ number_format($petiSisaAll) }} peti</p></div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b"><h3 class="text-sm font-semibold text-gray-700">Stok Per Gudang</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-2" rowspan="2">Gudang</th>
                        <th class="px-4 py-2 text-center border-b" colspan="5">Butir</th>
                        <th class="px-4 py-2 text-center border-b border-l border-gray-200" colspan="5">Peti</th>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 text-right">Masuk</th>
                        <th class="px-4 py-2 text-right">Keluar</th>
                        <th class="px-4 py-2 text-right">Eceran</th>
                        <th class="px-4 py-2 text-right">Sisa</th>
                        <th class="px-4 py-2 text-right">% Keluar</th>
                        <th class="px-4 py-2 text-right border-l border-gray-200">Masuk</th>
                        <th class="px-4 py-2 text-right">Keluar</th>
                        <th class="px-4 py-2 text-right">Eceran</th>
                        <th class="px-4 py-2 text-right">Sisa</th>
                        <th class="px-4 py-2 text-right">% Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapGudang as $r)
                    <tr class="border-b">
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $r->gudang->nama_gudang }}</td>
                        <td class="px-4 py-2 text-right text-indigo-600">{{ number_format($r->masuk) }}</td>
                        <td class="px-4 py-2 text-right text-orange-600">{{ number_format($r->keluar_sj) }}</td>
                        <td class="px-4 py-2 text-right text-amber-600">{{ number_format($r->eceran_butir) }}</td>
                        <td class="px-4 py-2 text-right font-semibold {{ $r->sisa < 0 ? 'text-red-600' : '' }}">{{ number_format($r->sisa) }}</td>
                        <td class="px-4 py-2 text-right text-gray-600">{{ $r->percent }}%</td>
                        <td class="px-4 py-2 text-right text-amber-600 border-l border-gray-200">{{ number_format($r->peti_masuk) }}</td>
                        <td class="px-4 py-2 text-right text-teal-600">{{ number_format($r->peti_keluar_sj) }}</td>
                        <td class="px-4 py-2 text-right text-amber-600">{{ number_format($r->eceran_peti) }}</td>
                        <td class="px-4 py-2 text-right font-semibold {{ $r->peti_sisa < 0 ? 'text-red-600' : '' }}">{{ number_format($r->peti_sisa) }}</td>
                        <td class="px-4 py-2 text-right text-gray-600">{{ $r->peti_percent }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center flex-wrap gap-3">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Telur Keluar</h2>
            <div class="flex space-x-2">
                <a href="{{ route('telur.keluar.kartu') }}" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Kartu Stok</a>
                <a href="{{ route('telur.keluar.create') }}" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">Input Telur Keluar</a>
            </div>
        </div>
        <form method="GET" class="p-4 border-b bg-gray-50 flex flex-wrap gap-3 items-end">
            <div><label class="block text-xs text-gray-600">Gudang</label><select name="gudang_id" class="mt-1 border-gray-300 rounded-md text-sm w-36"><option value="">Semua</option>@foreach($gudangs as $g)<option value="{{ $g->id }}" {{ $gudangId==$g->id?'selected':'' }}>{{ $g->nama_gudang }}</option>@endforeach</select></div>
            <div><label class="block text-xs text-gray-600">Cari</label><input type="text" name="search" value="{{ $search ?? '' }}" placeholder="No. SJ, Driver..." class="mt-1 border-gray-300 rounded-md text-sm w-40 px-2 py-1.5"></div>
            <div><label class="block text-xs text-gray-600">Dari</label><input type="date" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <div><label class="block text-xs text-gray-600">Sampai</label><input type="date" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm">Filter</button>
            <a href="{{ route('telur.keluar.index') }}" class="px-3 py-1.5 text-gray-600 text-sm">Reset</a>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Tgl</th>
                        <th class="px-4 py-3">No. SJ</th>
                        <th class="px-4 py-3">Gudang</th>
                        <th class="px-4 py-3">Driver</th>
                        <th class="px-4 py-3 text-right">Peti</th>
                        <th class="px-4 py-3 text-right">Butir</th>
                        <th class="px-4 py-3 text-right">Berat</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stoks as $s)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $s->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 font-mono text-xs">{{ $s->no_referensi ?: '-' }}</td>
                        <td class="px-4 py-2">{{ $s->gudang->nama_gudang ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $s->driver ?: '-' }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($s->peti) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($s->jumlah_butir) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($s->berat_kg, 1) }} kg</td>
                        <td class="px-4 py-2 text-center"> @if($s->penjualanStokDetails->isNotEmpty())
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">Terjual</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">Tersedia</span>
                                @endif</td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-1">
                               
                                <a href="{{ route('telur.keluar.surat-jalan', $s) }}" target="_blank" class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="Surat Jalan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></a>
                                <a href="{{ route('telur.keluar.edit', $s) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                <form action="{{ route('telur.keluar.destroy', $s) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-red-600 hover:bg-red-50 rounded" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-gray-500">Belum ada data telur keluar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $stoks->links() }}</div>
    </div>
</div>
@endsection
