@extends('layouts.admin')

@section('title', 'Review Setoran - SIKAP')
@section('page-title', 'Review Setoran per Shift')

@section('content')
<div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <div>
        <h2 class="text-lg font-bold text-gray-800">Review Setoran per Shift</h2>
        <p class="text-sm text-gray-500">Input sortasi untuk setiap shift yang masuk gudang</p>
    </div>
</div>

    <form method="GET" class="sticky top-0 z-30 bg-white rounded-lg shadow-sm border border-gray-100 px-6 py-3 mb-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-600 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-600 mb-1">Shift</label>
            <select name="shift" class="border-gray-300 rounded-md text-sm pl-3 pr-8 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                <option value="" {{ $shift == '' ? 'selected' : '' }}>Semua</option>
                <option value="siang" {{ $shift == 'siang' ? 'selected' : '' }}>Siang</option>
                <option value="sore" {{ $shift == 'sore' ? 'selected' : '' }}>Sore</option>
            </select>
        </div>
        @if(auth()->user()->hasRole('super_admin'))
        <div>
            <label class="block text-xs text-gray-600 mb-1">Gudang</label>
            <select name="gudang_id" class="border-gray-300 rounded-md text-sm pl-3 pr-8 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                <option value="">Semua Gudang</option>
                @foreach($gudangs as $g)
                <option value="{{ $g->id }}" {{ $gudangId == $g->id ? 'selected' : '' }}>{{ $g->nama_gudang }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition-colors">Filter</button>
            <a href="{{ route('setoran.review') }}" class="px-4 py-1.5 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-200 rounded-md transition-colors">Reset</a>
        </div>
    </form>

    @if($setorans->isNotEmpty())
    @foreach($setorans as $shiftName => $items)
    @php
        $shiftTotalButir = $items->sum('butir');
        $shiftTotalKarpet = $items->sum('karpet');
        $shiftTotalSisa = $items->sum(function($s) { return $s->produksiTelur->sisa ?? 0; });
        $shiftTotalTersortir = 0;
        $shiftTotalPeti = 0;
        $shiftTotalBerat = 0;
        $shiftSisa = 0;
        foreach ($items as $s) {
            $sort = $sortasiMap->get($s->kandang_id . '_' . $shiftName . '_' . $s->gudang_id);
            if ($sort) {
                $shiftTotalTersortir += $sort->detail->sum('butir');
                $shiftTotalPeti += $sort->detail->count();
                $shiftTotalBerat += $sort->detail->sum('berat');
                $shiftSisa += $sort->sisa;
            }
        }
        $sortasiSisa = $sortasiSisaMap->get($shiftName . '_' . ($gudangId ?: $items->first()->gudang_id));
        $shiftTotalSisaSortasi = $sortasiSisa->sisa ?? 0;
        $usedSisaCount = $sortasiSisa ? ($usedCounts->get($sortasiSisa->id) ?? 0) : 0;
        $sisaNotBalance = false;
        if ($sortasiSisa) {
            $gid = $gudangId ?: $items->first()->gudang_id;
            $expectedMasuk = 0;
            foreach ($items as $s) {
                if ($s->gudang_id != $gid) continue;
                $sort = $sortasiMap->get($s->kandang_id . '_' . $shiftName . '_' . $s->gudang_id);
                $expectedMasuk += $sort ? $sort->sisa : ($s->produksiTelur->sisa ?? 0);
            }
            if ($shiftName === 'sore') {
                $upstream = $sameDaySisaMap->get('siang_' . $gid);
                $expectedMasuk += $upstream ? $upstream->sisa : 0;
            } elseif ($shiftName === 'siang' && isset($prevSisaSortirMap)) {
                $prevUpstream = $prevSisaSortirMap->get('sore_' . $gid);
                $expectedMasuk += $prevUpstream ? $prevUpstream->sisa : 0;
            }
            $storedMasuk = $sortasiSisa->detail->sum('butir')
                + $sortasiSisa->pecah
                + $sortasiSisa->retak
                + $sortasiSisa->kopong
                + $sortasiSisa->sisa;
            if ($expectedMasuk != $storedMasuk) {
                $sisaNotBalance = true;
            }
        }
    @endphp
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-4">
        <div class="px-6 py-2.5 text-white rounded-t-lg flex items-center justify-between" style="background: {{ $shiftName === 'sore' ? '#ff9100' : '#3894ca' }}">
            <h4 class="text-xs font-semibold uppercase tracking-wider">Shift {{ ucfirst($shiftName) }}</h4>
            <span class="px-2 py-0.5 text-xs bg-white/20 rounded-full">{{ $items->count() }} kandang</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 ">
                <thead>
                    <tr>
                        <th colspan="4" class="px-3 py-2 text-xs font-semibold uppercase tracking-wider bg-gray-500 text-white text-center border-r-2 border-white/20">Setoran Kandang</th>
                        <th colspan="5" class="px-3 py-2 text-xs font-semibold uppercase tracking-wider bg-green-600 text-white text-center">Sortasi Gudang</th>
                    </tr>
                    <tr class="text-xs text-gray-700 uppercase bg-gray-50">
                        <th style="position:sticky;left:0;z-index:11;background:#f9fafb" class="px-3 py-2">Kandang</th>
                        <th class="px-3 py-2 text-right">Karpet</th>
                        <th class="px-3 py-2 text-right">Sisa Prosuksi (Butir)</th>
                        <th class="px-3 py-2 text-right border-r-2 border-gray-300">Total (Butir)</th>
                        <th class="px-3 py-2 text-right">Tersortir (Butir)</th>
                        <th class="px-3 py-2 text-right">Peti</th>
                        <th class="px-3 py-2 text-right">Berat (kg)</th>
                        <th class="px-3 py-2 text-right">Sisa Sortir (Butir)</th>
                        <th class="px-3 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $s)
                    @php
                        $sortasi = $sortasiMap->get($s->kandang_id . '_' . $shiftName . '_' . $s->gudang_id);
                        $detail = $sortasi ? $sortasi->detail : collect();
                        $tersortir = $detail->sum('butir');
                        $usedCount = $sortasi ? ($usedCounts->get($sortasi->id) ?? 0) : 0;
                    @endphp
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td style="position:sticky;left:0;z-index:10;background:white" class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $sortasi ? 'bg-green-500' : 'bg-amber-400' }}"></span>
                                <span class="font-medium text-gray-900">{{ $s->kandang->nama_kandang ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">{{ number_format($s->karpet) }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">{{ number_format($s->produksiTelur->sisa ?? 0) }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap font-medium border-r-2 border-gray-200">{{ number_format($s->butir) }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">{{ $tersortir > 0 ? number_format($tersortir) : '-' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap">{{ $detail->count() > 0 ? number_format($detail->count()) : '-' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap text-sky-600">{{ $detail->sum('berat') > 0 ? number_format($detail->sum('berat'), 2) : '-' }}</td>
                        <td class="px-3 py-2 text-right whitespace-nowrap text-gray-500">{{ $sortasi && $sortasi->sisa > 0 ? number_format($sortasi->sisa) : '-' }}</td>
                        <td class="px-3 py-2">
                            <div class="flex flex-col sm:flex-row gap-1">
                            @if(auth()->user()->hasAnyRole(['super_admin', 'petugas_gudang']))
                                @if($usedCount > 0)
                                    <span class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs font-medium text-center text-gray-600 bg-gray-100 border border-gray-200 rounded-lg">{{ $usedCount }} peti terpakai</span>
                                @else
                                    <a href="{{ route('setoran.sortasi', ['tanggal' => $tanggal, 'shift' => $shiftName, 'gudang_id' => $gudangId ?: $s->gudang_id, 'kandang_id' => $s->kandang_id]) }}"
                                        class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs font-medium text-center rounded-lg transition-all hover:shadow-md active:scale-95
                                            {{ $sortasi ? 'text-yellow-700 bg-yellow-50 border border-yellow-300 hover:bg-yellow-100' : 'text-white bg-indigo-600 hover:bg-indigo-700' }}">
                                        {{ $sortasi ? 'Edit' : 'Sortasi' }}
                                    </a>
                                @endif
                            @endif
                            @if($sortasi)
                            <a href="{{ route('setoran.detail', ['tanggal' => $tanggal, 'shift' => $shiftName, 'gudang_id' => $gudangId ?: $s->gudang_id, 'kandang_id' => $s->kandang_id]) }}"
                               class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs font-medium text-center text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-all hover:shadow-md active:scale-95">
                                Detail
                            </a>
                            @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-300 font-semibold text-gray-800 text-xs">
                    <tr>
                        <td style="position:sticky;left:0;z-index:10;background:#f9fafb" class="px-3 py-2">Total Kandang</td>
                        <td class="px-3 py-2 text-right">{{ number_format($shiftTotalKarpet) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($shiftTotalSisa) }}</td>
                        <td class="px-3 py-2 text-right border-r-2 border-gray-200">{{ number_format($shiftTotalButir) }}</td>
                        <td class="px-3 py-2 text-right">{{ $shiftTotalTersortir > 0 ? number_format($shiftTotalTersortir) : '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ $shiftTotalPeti > 0 ? number_format($shiftTotalPeti) : '-' }}</td>
                        <td class="px-3 py-2 text-right text-sky-600">{{ $shiftTotalBerat > 0 ? number_format($shiftTotalBerat, 2) : '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ $shiftSisa > 0 ? number_format($shiftSisa) : '-' }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
            @if($shiftSisa > 0)
            <div class="border-t border-slate-200">
                <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 ">
                    <thead>
                        <tr>
                            <th colspan="9" class="px-3 py-1.5 text-xs font-semibold uppercase tracking-wider bg-slate-100 text-slate-600">Sisa Sortir (Campuran)</th>
                        </tr>
                        <tr class="text-xs text-gray-700 uppercase bg-gray-50">
                            <th style="position:sticky;left:0;z-index:11;background:#f9fafb" class="px-3 py-2">Kandang</th>
                            <th class="px-3 py-2 text-right">Karpet</th>
                            <th class="px-3 py-2 text-right">Sisa Produksi</th>
                            <th class="px-3 py-2 text-right border-r-2 border-gray-300">Total Sisa Sortir (Butir)</th>
                            <th class="px-3 py-2 text-right">Tersortir (Butir)</th>
                            <th class="px-3 py-2 text-right">Peti</th>
                            <th class="px-3 py-2 text-right">Berat (kg)</th>
                            <th class="px-3 py-2 text-right">Sisa Sortir (Butir)</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white">
                            <td style="position:sticky;left:0;z-index:10;background:white" class="px-3 py-2 font-medium text-slate-700">Sisa Sortir</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap text-gray-400">-</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap text-gray-400">-</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap font-bold text-slate-700 border-r-2 border-gray-200">{{ number_format($shiftSisa) }}</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap">{{ $sortasiSisa && $sortasiSisa->detail->sum('butir') > 0 ? number_format($sortasiSisa->detail->sum('butir')) : '-' }}</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap">{{ $sortasiSisa && $sortasiSisa->detail->count() > 0 ? number_format($sortasiSisa->detail->count()) : '-' }}</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap text-sky-600">{{ $sortasiSisa && $sortasiSisa->detail->sum('berat') > 0 ? number_format($sortasiSisa->detail->sum('berat'), 2) : '-' }}</td>
                            <td class="px-3 py-2 text-right whitespace-nowrap text-gray-500">{{ $sortasiSisa && $sortasiSisa->sisa > 0 ? number_format($sortasiSisa->sisa) : '-' }}</td>
                            <td class="px-3 py-2">
                                <div class="flex flex-col sm:flex-row gap-1">
                                    @if($usedSisaCount > 0)
                                        <span class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs font-medium text-center text-gray-600 bg-gray-100 border border-gray-200 rounded-lg">{{ $usedSisaCount }} peti terpakai</span>
                                    @else
                                        <a href="{{ route('setoran.sortasi', ['tanggal' => $tanggal, 'shift' => $shiftName, 'gudang_id' => $gudangId ?: $items->first()->gudang_id]) }}"
                                           class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs font-medium text-center rounded-lg transition-all hover:shadow-sm active:scale-95
                                               {{ $sortasiSisa ? 'text-yellow-700 bg-yellow-50 border border-yellow-300 hover:bg-yellow-100' : 'text-white bg-indigo-600 hover:bg-indigo-700' }}">
                                            {{ $sortasiSisa ? 'Edit' : 'Sortir Sisa' }}
                                        </a>
                                    @endif
                                    @if($sortasiSisa)
                                    <a href="{{ route('setoran.detail', ['tanggal' => $tanggal, 'shift' => $shiftName, 'gudang_id' => $gudangId ?: $items->first()->gudang_id]) }}"
                                       class="px-2 py-1 sm:px-3 sm:py-1.5 text-xs font-medium text-center text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-all hover:shadow-sm active:scale-95">
                                        Detail
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
            @endif
            @if($sisaNotBalance)
            <div class="px-6 py-3 bg-orange-50 border-t border-orange-200">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-orange-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-sm text-orange-800 font-medium">Sisa Sortir tidak balance</p>
                        <p class="text-xs text-orange-600">Total Sisa Sortir tidak sesuai dengan data sortir per-kandang/carryover. Silakan <strong>sortir ulang Sisa Sortir</strong>.</p>
                    </div>
                </div>
            </div>
            @endif
            @if($sortasiSisa)
            <div class="px-6 pt-6 pb-6 bg-gradient-to-r from-slate-50 to-gray-50 border-t border-slate-200 rounded-b-lg">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-sm font-semibold text-slate-700">Detail Sisa Sortir</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg px-3 py-2.5 text-center shadow-sm border border-red-100">
                        <p class="text-xs text-red-500 uppercase tracking-wide font-medium">Pecah</p>
                        <p class="text-xl font-bold text-red-600">{{ number_format($sortasiSisa->pecah) }}</p>
                    </div>
                    <div class="bg-white rounded-lg px-3 py-2.5 text-center shadow-sm border border-orange-100">
                        <p class="text-xs text-orange-500 uppercase tracking-wide font-medium">Retak</p>
                        <p class="text-xl font-bold text-orange-600">{{ number_format($sortasiSisa->retak) }}</p>
                    </div>
                    <div class="bg-white rounded-lg px-3 py-2.5 text-center shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Kopong</p>
                        <p class="text-xl font-bold text-gray-600">{{ number_format($sortasiSisa->kopong) }}</p>
                    </div>
                    <div class="bg-white rounded-lg px-3 py-2.5 text-center shadow-sm border border-gray-100 border-l-2 border-l-green-500">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Sisa / Stock (Butir)</p>
                        <p class="text-xl font-bold text-green-600">{{ number_format($sortasiSisa->sisa) }}</p>
                    </div>
                </div>
            </div>
            @endif
    </div>
    @endforeach
    @if($perKandang->isNotEmpty())
    <div class="text-xs text-gray-400 uppercase tracking-wide font-semibold pl-1 mb-2 mt-8">↳ Rekap Per Kandang</div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-2.5 bg-indigo-600 text-white rounded-t-lg flex items-center justify-between">
            <h4 class="text-xs font-semibold uppercase tracking-wider">Total Per Kandang (Semua Shift)</h4>
            <span class="px-2 py-0.5 text-xs bg-white/20 rounded-full">{{ $perKandang->count() }} kandang</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 ">
                <thead>
                    <tr>
                        <th colspan="4" class="px-3 py-2 text-xs font-semibold uppercase tracking-wider bg-gray-500 text-white text-center border-r-2 border-white/20">Setoran Kandang</th>
                        <th colspan="4" class="px-3 py-2 text-xs font-semibold uppercase tracking-wider bg-green-600 text-white text-center">Sortasi Gudang</th>
                    </tr>
                    <tr class="text-xs text-gray-700 uppercase bg-gray-50">
                        <th style="position:sticky;left:0;z-index:11;background:#f9fafb" class="px-3 py-2">Kandang</th>
                        <th class="px-3 py-2 text-right">Karpet</th>
                        <th class="px-3 py-2 text-right">Sisa Prosuksi (Butir)</th>
                        <th class="px-3 py-2 text-right border-r-2 border-gray-300">Total (Butir)</th>
                        <th class="px-3 py-2 text-right">Tersortir (Butir)</th>
                        <th class="px-3 py-2 text-right">Peti</th>
                        <th class="px-3 py-2 text-right">Berat (kg)</th>
                        <th class="px-3 py-2 text-right">Sisa Sortir (Butir)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($perKandang as $pk)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td style="position:sticky;left:0;z-index:10;background:white" class="px-3 py-2 font-medium text-gray-900">{{ $pk->kandang->nama_kandang ?? '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($pk->karpet) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($pk->sisa) }}</td>
                        <td class="px-3 py-2 text-right font-medium border-r-2 border-gray-200">{{ number_format($pk->butir) }}</td>
                        <td class="px-3 py-2 text-right">{{ $pk->tersortir > 0 ? number_format($pk->tersortir) : '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ $pk->peti > 0 ? number_format($pk->peti) : '-' }}</td>
                        <td class="px-3 py-2 text-right text-sky-600">{{ $pk->berat > 0 ? number_format($pk->berat, 2) : '-' }}</td>
                        <td class="px-3 py-2 text-right text-gray-500">{{ $pk->sisa_sortasi > 0 ? number_format($pk->sisa_sortasi) : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-300 font-semibold text-gray-800 text-xs">
                    @php
                        $totButir = $perKandang->sum('butir');
                        $totKarpet = $perKandang->sum('karpet');
                        $totSisa = $perKandang->sum('sisa');
                        $totTersortir = $perKandang->sum('tersortir');
                        $totPeti = $perKandang->sum('peti');
                        $totBerat = $perKandang->sum('berat');
                        $totSisaSortasi = $perKandang->sum('sisa_sortasi');
                    @endphp
                    <tr>
                        <td style="position:sticky;left:0;z-index:10;background:#f9fafb" class="px-3 py-2">Total</td>
                        <td class="px-3 py-2 text-right">{{ number_format($totKarpet) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($totSisa) }}</td>
                        <td class="px-3 py-2 text-right border-r-2 border-gray-200">{{ number_format($totButir) }}</td>
                        <td class="px-3 py-2 text-right">{{ $totTersortir > 0 ? number_format($totTersortir) : '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ $totPeti > 0 ? number_format($totPeti) : '-' }}</td>
                        <td class="px-3 py-2 text-right text-sky-600">{{ $totBerat > 0 ? number_format($totBerat, 2) : '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ $totSisaSortasi > 0 ? number_format($totSisaSortasi) : '-' }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
    @if($rekapSisa->total_sisa > 0)
    <div class="text-xs text-gray-400 uppercase tracking-wide font-semibold pl-1 mb-2 mt-8">↳ Rekap Per Sisa Sortir</div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mb-4">
        <div class="px-6 py-2.5 bg-indigo-600 text-white rounded-t-lg flex items-center justify-between">
            <h4 class="text-xs font-semibold uppercase tracking-wider">Rekap Sisa Sortir (Campuran)</h4>
            <span class="px-2 py-0.5 text-xs bg-white/20 rounded-full">Semua Shift</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-right">Total Sisa (Butir)</th>
                        <th class="px-3 py-2 text-right">Tersortir (Butir)</th>
                        <th class="px-3 py-2 text-right">Peti</th>
                        <th class="px-3 py-2 text-right">Berat (kg)</th>
                        <th class="px-3 py-2 text-right">Pecah</th>
                        <th class="px-3 py-2 text-right">Retak</th>
                        <th class="px-3 py-2 text-right">Kopong</th>
                        <th class="px-3 py-2 text-right">Sisa (Butir)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white">
                        <td class="px-3 py-2 text-right font-bold text-slate-700">{{ number_format($rekapSisa->total_sisa) }}</td>
                        <td class="px-3 py-2 text-right">{{ $rekapSisa->tersortir > 0 ? number_format($rekapSisa->tersortir) : '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ $rekapSisa->peti > 0 ? number_format($rekapSisa->peti) : '-' }}</td>
                        <td class="px-3 py-2 text-right text-sky-600">{{ $rekapSisa->berat > 0 ? number_format($rekapSisa->berat, 2) : '-' }}</td>
                        <td class="px-3 py-2 text-right text-red-600">{{ $rekapSisa->pecah > 0 ? number_format($rekapSisa->pecah) : '-' }}</td>
                        <td class="px-3 py-2 text-right text-orange-600">{{ $rekapSisa->retak > 0 ? number_format($rekapSisa->retak) : '-' }}</td>
                        <td class="px-3 py-2 text-right text-gray-500">{{ $rekapSisa->kopong > 0 ? number_format($rekapSisa->kopong) : '-' }}</td>
                        <td class="px-3 py-2 text-right text-gray-500">{{ $rekapSisa->sisa > 0 ? number_format($rekapSisa->sisa) : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @else
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 min-h-[50vh] flex items-center justify-center">
    <div class="text-center py-16">
        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        <p class="mt-4 text-gray-500 font-medium">Belum ada setoran untuk tanggal ini</p>
        <p class="text-sm text-gray-400 mt-1">Setoran dari petugas kandang akan muncul di sini</p>
        
    </div>
    </div>
    @endif
@endsection
