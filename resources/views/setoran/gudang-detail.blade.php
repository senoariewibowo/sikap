@extends('layouts.admin')
@section('title', 'Stok Gudang - SIKAP')
@section('page-title', 'Stok Telur Gudang')
@section('content')

<div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <div>
        <h2 class="text-lg font-bold text-gray-800">Stok Gudang: {{ $gudang->nama_gudang }} ({{ $gudang->kode_gudang }})</h2>
        <p class="text-sm text-gray-500">{{ now()->format('d M Y') }}</p>
    </div>
    @if(auth()->user()->hasRole('super_admin'))
    <a href="{{ route('setoran.gudang') }}" class="ml-auto px-3 py-1.5 text-sm text-gray-600 border rounded-lg hover:bg-gray-50">← Kembali</a>
    @endif
</div>

<div class="space-y-6">

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Setoran Masuk</h3>
    </div>
    <div class="p-5 grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Karpet (Tray)</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($kpis['setoran_karpet']) }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Total (Butir)</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($kpis['setoran_butir']) }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Final Stock (Hasil Sortir)</h3>
        @php $balance = ($kpis['setoran_butir'] ?? 0) - ($kpis['sortir_tersortir'] ?? 0) - ($kpis['sortir_sisa'] ?? 0); @endphp
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $balance == 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $balance == 0 ? 'bg-green-500' : 'bg-red-500' }}"></span>
            {{ $balance == 0 ? 'Balance ✓' : 'Selisih ' . number_format(abs($balance)) }}
        </span>
    </div>
    <div class="p-5 space-y-4">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-3">Satuan Per Peti</p>
                @php
                    $custKeluar = $stokStats['customer_keluar'] ?? 0;
                    $custPeti = $stokStats['customer_keluar_peti'] ?? 0;
                    $custBerat = $stokStats['customer_keluar_berat'] ?? 0;
                    $eceranAlloc = $stokStats['eceran_allocated'] ?? 0;
                    $eceranPeti = $stokStats['eceran_peti'] ?? 0;
                    $eceranBerat = $stokStats['eceran_berat'] ?? 0;
                    $stokNet = ($kpis['final_tersortir'] ?? 0) - $custKeluar - $eceranAlloc;
                    $petiNet = ($kpis['final_peti'] ?? 0) - $custPeti - $eceranPeti;
                    $beratNet = ($kpis['final_berat'] ?? 0) - $custBerat - $eceranBerat;
                @endphp
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Tersedia</p>
                        <p class="text-xl font-bold {{ $stokNet >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($stokNet) }} <span class="text-xs font-normal text-gray-400">butir</span></p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ number_format($kpis['final_tersortir']) }} - {{ number_format($custKeluar) }} - {{ number_format($eceranAlloc) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Peti</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($petiNet) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ number_format($kpis['final_peti']) }} - {{ number_format($custPeti) }} - {{ number_format($eceranPeti) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Berat</p>
                        <p class="text-xl font-bold text-sky-600">{{ number_format($beratNet, 2) }} <span class="text-xs font-normal text-gray-400">kg</span></p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ number_format($kpis['final_berat'], 2) }} - {{ number_format($custBerat, 2) }} - {{ number_format($eceranBerat, 2) }}</p>
                    </div>
                @php 
                    $sisaAkhir = $kpis['batch_sisa'] ?? 0;
                    $batchDone = ($kpis['batch_tersortir'] ?? 0) > 0 || ($kpis['batch_peti'] ?? 0) > 0;
                @endphp
                <div class="bg-indigo-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-indigo-500">Sisa Sortir</p>
                    @if($batchDone)
                    <p class="text-xl font-bold text-indigo-600">{{ number_format($sisaAkhir) }} <span class="text-xs font-normal text-gray-400">butir</span></p>
                    @else
                    <p class="text-xl font-bold text-gray-400">— <span class="text-xs font-normal">Belum</span></p>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-3">Pemotongan Cacat</p>
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-red-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-red-500">Pecah</p>
                    <p class="text-xl font-bold text-red-600">{{ number_format($kpis['batch_pecah']) }}</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-orange-500">Retak</p>
                    <p class="text-xl font-bold text-orange-600">{{ number_format($kpis['batch_retak']) }}</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Kopong</p>
                    <p class="text-xl font-bold text-gray-600">{{ number_format($kpis['batch_kopong']) }}</p>
                </div>
            </div>
        </div>

        @php $totalFinal = ($kpis['final_tersortir'] ?? 0) + $sisaAkhir; $stokFinal = $totalFinal - $custKeluar - $eceranAlloc; @endphp
        <div class="bg-indigo-50 rounded-lg p-3 text-center">
            <p class="text-xs text-indigo-500 uppercase tracking-wider font-semibold">Total Final Stok (Butir)</p>
            <p class="text-xl font-bold text-indigo-600">{{ number_format($stokFinal) }} <span class="text-xs font-normal text-gray-400">butir</span></p>
            <p class="text-xs text-gray-400 mt-0.5">{{ number_format($totalFinal) }} - {{ number_format($custKeluar) }} - {{ number_format($eceranAlloc) }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Alokasi Stok Eceran</h3>
        <a href="{{ route('telur.eceran.create') }}" class="inline-flex items-center gap-1 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 font-semibold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Alokasi Eceran</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-max text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">No. Ref</th>
                    <th class="px-4 py-2">Gudang</th>
                    <th class="px-4 py-2 text-right">Butir</th>
                    <th class="px-4 py-2 text-right">Terjual</th>
                    <th class="px-4 py-2 text-right">Sisa</th>
                    <th class="px-4 py-2">Ket</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eceranAllocations as $a)
                @php $terjual = $a->transaksis->sum('jumlah_butir'); $sisa = $a->jumlah_butir - $terjual; @endphp
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap">{{ $a->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 font-mono text-xs">{{ $a->no_referensi }}</td>
                    <td class="px-4 py-2">{{ $a->gudang->nama_gudang ?? '-' }}</td>
                    <td class="px-4 py-2 text-right font-semibold">{{ number_format($a->jumlah_butir) }}</td>
                    <td class="px-4 py-2 text-right text-amber-600">{{ number_format($terjual) }}</td>
                    <td class="px-4 py-2 text-right {{ $sisa > 0 ? 'text-green-600' : 'text-gray-400' }}">{{ number_format($sisa) }}</td>
                    <td class="px-4 py-2 text-xs text-gray-400 max-w-[120px] truncate">{{ $a->keterangan }}</td>
                    <td class="px-4 py-2">
                        <div class="flex space-x-1">
                            <a href="{{ route('telur.eceran.edit', $a) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('telur.eceran.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus alokasi ini?')">
                                @csrf @method('DELETE')
                                <button class="p-1 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">Belum ada alokasi eceran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($eceranAllocations->hasPages())
    <div class="px-5 py-3 border-t">{{ $eceranAllocations->links() }}</div>
    @endif
</div>
</div>
@endsection
