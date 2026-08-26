@extends('layouts.admin')

@section('title', 'Detail Sortasi - SIKAP')
@section('page-title', 'Detail Sortasi per Peti')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                {{ $isSisaProduksi ? 'Detail Sisa Sortir Shift ' . ucfirst($shift) . ' (' . ($gudang->nama_gudang ?? '') . ')' : 'Detail Sortasi Shift ' . ucfirst($shift) }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                @if($gudang && !$isSisaProduksi) &middot; {{ $gudang->nama_gudang }} @endif
                @if($kandang) &middot; {{ $kandang->nama_kandang }} @endif
            </p>
        </div>
        <a href="{{ route('setoran.review', ['tanggal' => $tanggal, 'shift' => $shift, 'gudang_id' => $gudangId]) }}"
           class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
            &larr; Kembali
        </a>
    </div>

    <div class="p-6 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-wrap gap-4 text-sm">
            <div>
                <span class="text-gray-500">Setoran Masuk:</span>
                <span class="font-bold text-gray-800 ml-1">{{ number_format($sortasi->butirMasuk()) }} butir</span>
            </div>
            <div>
                <span class="text-gray-500">Tersortir:</span>
                <span class="font-bold text-indigo-600 ml-1">{{ number_format($tersortir) }} butir</span>
            </div>
            <div>
                <span class="text-gray-500">Total Peti:</span>
                <span class="font-bold text-gray-800 ml-1">{{ $sortasi->detail->count() }}</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-3 py-3">Peti</th>
                    <th class="px-3 py-3">Kode Peti</th>
                    <th class="px-3 py-3 text-right">Butir</th>
                    <th class="px-3 py-3 text-right">Karpet</th>
                    <th class="px-3 py-3 text-right">Berat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sortasi->detail as $i => $d)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-3 py-2 font-medium text-gray-900">{{ $i + 1 }}</td>
                    <td class="px-3 py-2"><span class="px-2 py-1 text-xs font-bold bg-indigo-100 text-indigo-700 rounded">{{ $d->kode_peti ?: '-' }}</span></td>
                    <td class="px-3 py-2 text-right">{{ number_format($d->butir) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($d->karpet) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($d->berat, 2) }} kg</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 font-medium text-gray-700">
                <tr>
                    <td class="px-3 py-2">{{ $sortasi->detail->count() }} peti</td>
                    <td class="px-3 py-2 text-right">{{ number_format($tersortir) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($sortasi->detail->sum('karpet')) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($sortasi->detail->count() * 15, 1) }} kg</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="p-6 border-t border-gray-200">
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-red-50 rounded-lg p-3 text-center">
                <p class="text-xs text-red-600">Pecah</p>
                <p class="text-lg font-bold text-red-700">{{ number_format($sortasi->pecah) }}</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-3 text-center">
                <p class="text-xs text-orange-600">Retak</p>
                <p class="text-lg font-bold text-orange-700">{{ number_format($sortasi->retak) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3 text-center">
                <p class="text-xs text-gray-500">Kopong</p>
                <p class="text-lg font-bold text-gray-700">{{ number_format($sortasi->kopong) }}</p>
            </div>
        </div>

        <div class="bg-indigo-50 rounded-lg p-4">
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div>
                    <span class="text-gray-600">Telur Bagus:</span>
                    <span class="font-bold text-gray-800 ml-1">{{ number_format(max(0, $telurBagus)) }} butir</span>
                </div>
                <div>
                    <span class="text-gray-600">Sisa:</span>
                    <span class="font-bold text-orange-600 ml-1">{{ number_format($sisa) }} butir</span>
                    <span class="text-xs text-gray-400 ml-1">→ dikumpulkan di Sisa Sortir</span>
                </div>
                <div>
                    <span class="text-gray-600">Stok Tersedia:</span>
                    <span class="font-bold text-indigo-600 ml-1">{{ number_format($tersortir) }} butir</span>
                </div>
            </div>
        </div>

        @if($sortasi->catatan)
        <div class="mt-4">
            <span class="text-xs text-gray-500">Catatan:</span>
            <p class="text-sm text-gray-700 mt-1">{{ $sortasi->catatan }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
