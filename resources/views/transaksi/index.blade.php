@extends('layouts.admin')

@section('title', 'Transaksi Driver - SIKAP')
@section('page-title', 'Transaksi')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center flex-wrap gap-3">
            <h2 class="text-lg font-semibold text-gray-800">Surat Jalan untuk Anda</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">No SJ</th>
                        <th class="px-4 py-3">Gudang</th>
                        <th class="px-4 py-3 text-right">Total Butir</th>
                        <th class="px-4 py-3 text-right">Sisa Butir</th>
                        <th class="px-4 py-3 text-right">Total Peti</th>
                        <th class="px-4 py-3 text-right">Sisa Peti</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sjs as $sj)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $sj->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 font-medium">{{ $sj->no_referensi ?: '-' }}</td>
                        <td class="px-4 py-2">{{ $sj->gudang->nama_gudang ?? '-' }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($sj->total_butir) }}</td>
                        <td class="px-4 py-2 text-right {{ $sj->sisa_butir > 0 ? 'text-orange-600 font-semibold' : '' }}">{{ number_format($sj->sisa_butir) }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($sj->total_peti) }}</td>
                        <td class="px-4 py-2 text-right {{ $sj->sisa_peti > 0 ? 'text-orange-600 font-semibold' : '' }}">{{ number_format($sj->sisa_peti) }}</td>
                        <td class="px-4 py-2">
                            <div class="flex space-x-1">
                                <a href="{{ route('transaksi.surat-jalan', $sj) }}" target="_blank" class="px-2 py-1 text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100">Detail</a>
                                @if($sj->is_latest)
                                <a href="{{ route('penjualan.create', ['stok_telur_keluar_id' => $sj->id]) }}" class="px-2 py-1 text-xs text-white bg-indigo-600 rounded hover:bg-indigo-700">Buat Penjualan</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-gray-500">Belum ada surat jalan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
