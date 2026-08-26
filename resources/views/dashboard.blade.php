@extends('layouts.admin')

@section('title', 'Dashboard - SIKAP')
@section('page-title', 'Dashboard')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
@if($stokMenipis->isNotEmpty())
<div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
    <div class="flex items-center space-x-2 mb-2">
        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        <h3 class="text-sm font-semibold text-red-800">Stok Menipis</h3>
    </div>
    <div class="flex flex-wrap gap-2">
        @foreach($stokMenipis as $item)
        <a href="{{ route('pakan.stok.index', ['kategori' => $item->kategori]) }}" class="px-3 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium hover:bg-red-200 transition">
            {{ $item->nama }}: <strong>@jumlah($item->stokSekarang())</strong> {{ $item->satuan }} (min: @jumlah($item->stok_minimal))
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Petugas Gudang Dashboard --}}
@if(auth()->user()->hasRole('petugas_gudang'))
<div class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Stok Telur Gudang</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($stokTelurGudang) }}</p>
            <p class="text-xs text-gray-400">Butir tersedia</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Total Setoran (Periode)</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($totalSetoran) }}</p>
            <p class="text-xs text-gray-400">Butir diterima</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Pending Setoran</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($pendingSetoran) }}</p>
            <p class="text-xs text-gray-400">Produksi belum disetor</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 flex items-center justify-center">
            <a href="{{ route('setoran.review') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Setoran Baru
            </a>
        </div>
    </div>
</div>

@if($gudangPakanData->isNotEmpty())
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-800">Stok Pakan & Obat di Gudang</h3>
    </div>
    <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach($gudangPakanData as $item)
        <div class="border border-gray-200 rounded-lg p-3 {{ $item->menipis ? 'border-red-300 bg-red-50' : '' }}">
            <p class="text-xs text-gray-500">{{ $item->jenis->nama }}</p>
            <p class="text-lg font-bold {{ $item->menipis ? 'text-red-600' : 'text-gray-800' }}">{{ number_format($item->stok, 1) }}</p>
            <p class="text-xs text-gray-400">{{ $item->jenis->satuan }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

@if($recentSetoran->isNotEmpty())
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-sm font-semibold text-gray-800">Setoran Terbaru</h3>
        <a href="{{ route('setoran.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800">Lihat semua &rarr;</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">Kandang</th>
                    <th class="px-4 py-2 text-center">Shift</th>
                    <th class="px-4 py-2 text-right">Jml Diterima</th>
                    <th class="px-4 py-2 text-right">Karpet</th>
                    <th class="px-4 py-2 text-right">Sisa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSetoran as $s)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($s->tanggal_setor)->format('d/m/Y') }}</td>
                    <td class="px-4 py-2">{{ $s->kandang->nama_kandang ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">{{ $s->produksiTelur->shift ?? '-' }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($s->butir) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($s->produksiTelur->karpet ?? 0) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($s->produksiTelur->sisa ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endif

{{-- Admin Selisih Alert --}}
@if(auth()->user()->hasRole('super_admin') && $setoranSelisihList->isNotEmpty())
<div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <div class="flex items-center space-x-2 mb-2">
        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        <h3 class="text-sm font-semibold text-yellow-800">Selisih Setoran > 2%</h3>
    </div>
    <div class="flex flex-wrap gap-2">
        @foreach($setoranSelisihList as $s)
        <a href="{{ route('setoran.index', ['gudang_id' => $s->gudang_id, 'kandang_id' => $s->kandang_id]) }}" class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium hover:bg-yellow-200">
            {{ $s->kandang->nama_kandang ?? '-' }}: {{ $s->selisih >= 0 ? '+' : '' }}{{ $s->selisih }} butir
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Admin Stok Gudang --}}
@if(auth()->user()->hasRole('super_admin') && $gudangStokSummary->isNotEmpty())
<div class="mb-6 bg-white rounded-lg shadow">
    <div class="p-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-800">Stok Telur per Gudang</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Gudang</th>
                    <th class="px-4 py-2 text-right">Butir</th>
                    <th class="px-4 py-2 text-right">Berat (kg)</th>
                    <th class="px-4 py-2 text-right">Karpet</th>
                    <th class="px-4 py-2 text-right">Peti</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gudangStokSummary as $gs)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium text-gray-900">{{ $gs->gudang->nama_gudang }}</td>
                    <td class="px-4 py-2 text-right font-bold">{{ number_format($gs->stok_butir) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($gs->stok_berat, 1) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($gs->stok_karpet) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($gs->stok_peti) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<form method="GET" class="mb-6 bg-white rounded-lg shadow p-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-600">Dari</label>
        <input type="date" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-md text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600">Sampai</label>
        <input type="date" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-md text-sm">
    </div>
    <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Filter</button>
    <a href="{{ route('dashboard') }}" class="px-4 py-1.5 text-gray-600 text-sm hover:bg-gray-100 rounded-md">Reset</a>
    <span class="text-xs text-gray-400 ml-auto">{{ \Carbon\Carbon::parse($dari)->format('d/m') }} – {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }} ({{ $days }} hari)</span>
</form>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
    <div class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Kandang</p>
        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalKandang }}</p>
        <p class="text-xs text-gray-400">Kandang Aktif</p>
    </div>
    @if(auth()->user()->hasRole('super_admin'))
    <div class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Gudang</p>
        <p class="text-2xl font-bold text-sky-600 mt-1">{{ $totalGudang }}</p>
        <p class="text-xs text-gray-400">Gudang Aktif</p>
    </div>
    <div class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Stok Telur</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($gudangStokSummary->sum('stok_butir')) }}</p>
        <p class="text-xs text-gray-400">Total butir di gudang</p>
    </div>
    @endif
    <div class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Populasi</p>
        <p class="text-2xl font-bold text-teal-600 mt-1">{{ number_format($totalPopulasi) }}</p>
        <p class="text-xs text-gray-400">Total Ekor</p>
    </div>
    <div class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Setoran</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($produksi) }}</p>
        <p class="text-xs text-gray-400"><span title="Hen Day Production — persentase telur dari total ayam aktif. HDP = (Telur / Populasi) × 100%. ≥90% sangat baik, ≥80% baik, <80% perlu investigasi." class="underline decoration-dotted cursor-help">HDP {{ $hdpRata }}%</span></p>
    </div>
    <div class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Tersortir</p>
        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($tersortir) }}</p>
        <p class="text-xs text-gray-400">{{ $produksi > 0 ? round($tersortir / $produksi * 100) : 0 }}% dari produksi</p>
    </div>
    <div class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Telur Keluar</p>
        <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($telurKeluar) }}</p>
        <p class="text-xs text-gray-400">{{ $produksi > 0 ? round($telurKeluar / $produksi * 100) : 0 }}% dari panen</p>
    </div>
    <div class="bg-white rounded-lg shadow p-3 hover:shadow-md transition">
        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Mortalitas</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($mati + $afkir) }}</p>
        <p class="text-xs text-gray-400">Mati {{ $mati }} / Afkir {{ $afkir }}</p>
    </div>
</div>

@if(auth()->user()->hasRole('super_admin'))
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 uppercase tracking-wider font-semibold">Omzet Periode</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($omzet, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ number_format($butirTerjual) }} butir terjual &middot; <span class="text-red-500">Piutang: Rp {{ number_format($piutang, 0, ',', '.') }}</span></p>
                <p class="text-xs text-gray-400 mt-1">Total semua waktu: <strong class="text-gray-700">Rp {{ number_format($omzetTotal, 0, ',', '.') }}</strong></p>
            </div>
            <div class="p-3 rounded-full bg-emerald-100 text-emerald-600 shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    @if(auth()->user()->hasRole('super_admin'))
    <div class="bg-white rounded-lg shadow p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 uppercase tracking-wider font-semibold">Pengeluaran Periode</p>
                <p class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">Rata-rata: Rp {{ number_format($pengeluaran / max(1, $days), 0, ',', '.') }} /hari</p>
                <p class="text-xs text-gray-400 mt-1">{{ $days }} hari dalam rentang</p>
            </div>
            <div class="p-3 rounded-full bg-red-100 text-red-600 shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-400 uppercase tracking-wider font-semibold">Produksi Telur</p>
            <div class="p-2 rounded-full bg-amber-100 text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-amber-600 mb-1">{{ number_format($produksi) }} <span class="text-sm font-normal text-gray-400">butir</span></div>
        <p class="text-sm text-gray-500"><span title="Hen Day Production" class="underline decoration-dotted cursor-help">HDP {{ $hdpRata }}%</span></p>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full text-xs">
                <thead><tr class="border-b text-gray-400"><th class="py-1 text-left font-medium">Kandang</th><th class="text-right font-medium">Butir</th></tr></thead>
                <tbody>
                    @foreach($snapshot as $sn)
                    <tr class="border-b border-gray-100">
                        <td class="py-1 text-gray-600">{{ $sn->kandang->nama_kandang }}</td>
                        <td class="py-1 text-right">{{ $sn->produksi > 0 ? number_format($sn->produksi) : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-5 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-gray-400 uppercase tracking-wider font-semibold">Populasi Ayam</p>
            <div class="p-2 rounded-full bg-teal-100 text-teal-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            </div>
        </div>
        <div class="text-2xl font-bold text-teal-600 mb-1">{{ number_format($totalPopulasi) }} <span class="text-sm font-normal text-gray-400">ekor</span></div>
        <p class="text-sm text-gray-500">Kapasitas: <strong>{{ number_format($kapasitasTotal) }}</strong> &middot; Utilisasi <strong>{{ $utilisasi }}%</strong></p>
        <div class="mt-3 space-y-1 text-sm text-gray-600">
            <p>Masuk: <strong>{{ number_format($masukTotal) }}</strong> &middot; Mati: <strong class="text-red-600">{{ number_format($mati) }}</strong> &middot; Afkir: <strong class="text-orange-600">{{ number_format($afkir) }}</strong></p>
            @if($mortalitasRate > 0)
            <p class="text-xs text-gray-400">Mortalitas: {{ $mortalitasRate }}%</p>
            @endif
        </div>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full text-xs">
                <thead><tr class="border-b text-gray-400"><th class="py-1 text-left font-medium">Kandang</th><th class="text-right font-medium">Pop</th><th class="text-right font-medium">Kap</th><th class="text-right font-medium">Util</th><th class="text-right font-medium">Msk</th><th class="text-right font-medium">Mati</th><th class="text-right font-medium">Afkir</th></tr></thead>
                <tbody>
                    @foreach($snapshot as $sn)
                    <tr class="border-b border-gray-100">
                        <td class="py-1 text-gray-600">{{ $sn->kandang->nama_kandang }}</td>
                        <td class="py-1 text-right">{{ number_format($sn->populasi) }}</td>
                        <td class="py-1 text-right">{{ number_format($sn->kapasitas) }}</td>
                        <td class="py-1 text-right">{{ $sn->utilisasi }}%</td>
                        <td class="py-1 text-right">{{ $sn->masuk > 0 ? number_format($sn->masuk) : '-' }}</td>
                        <td class="py-1 text-right text-red-500">{{ $sn->mati > 0 ? $sn->mati : '-' }}</td>
                        <td class="py-1 text-right text-orange-500">{{ $sn->afkir > 0 ? $sn->afkir : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Tren Produksi</h3>
        <canvas id="chartProduksi" height="100"></canvas>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    @if(auth()->user()->hasRole('petugas_kandang') && !empty($chartPemakaianDatasets))
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Penggunaan Pakan & Obat <span class="text-xs text-gray-400 font-normal">Total: @jumlah($pemakaianTotal) kg/satuan</span></h3>
        <canvas id="chartPemakaian" height="80"></canvas>
    @else
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Mortalitas</h3>
        <canvas id="chartMortalitas" height="80"></canvas>
    @endif
</div>

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="p-4 border-b"><h3 class="text-sm font-semibold text-gray-700">Snapshot Per Kandang</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-2">Kandang</th>
                    <th class="px-4 py-2 text-right">Populasi</th>
                    <th class="px-4 py-2 text-right">Produksi</th>
                    <th class="px-4 py-2 text-right"><span title="Hen Day Production = (Telur / Populasi) × 100%" class="underline decoration-dotted cursor-help">HDP%</span></th>
                    <th class="px-4 py-2 text-right">Keluar</th>
                    <th class="px-4 py-2 text-right">Mati</th>
                    <th class="px-4 py-2 text-right">Afkir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($snapshot as $sn)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium text-gray-900"><a href="{{ route('kandang.show', $sn->kandang) }}" class="hover:text-indigo-600 hover:underline">{{ $sn->kandang->nama_kandang }}</a></td>
                    <td class="px-4 py-2 text-right">{{ number_format($sn->populasi) }}</td>
                    <td class="px-4 py-2 text-right {{ $sn->produksi > 0 ? 'text-amber-700 font-medium' : 'text-gray-400' }}">{{ number_format($sn->produksi) }}</td>
                    <td class="px-4 py-2 text-right">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sn->hdp >= 90 ? 'bg-green-100 text-green-800' : ($sn->hdp >= 80 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $sn->hdp }}%</span>
                    </td>
                    <td class="px-4 py-2 text-right">{{ $sn->keluar > 0 ? number_format($sn->keluar) : '-' }}</td>
                    <td class="px-4 py-2 text-right {{ $sn->mati > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">{{ $sn->mati > 0 ? $sn->mati : '-' }}</td>
                    <td class="px-4 py-2 text-right {{ $sn->afkir > 0 ? 'text-orange-600 font-medium' : 'text-gray-400' }}">{{ $sn->afkir > 0 ? $sn->afkir : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada data kandang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-5 gap-3">
    @if(!auth()->user()->hasRole('viewer'))
    <a href="{{ route('populasi.create') }}" class="flex items-center space-x-3 p-4 bg-white rounded-lg shadow hover:shadow-md transition border border-gray-100">
        <div class="p-2 rounded-full bg-teal-100 text-teal-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
        <div><h4 class="font-medium text-sm text-gray-800">Input Populasi</h4><p class="text-xs text-gray-400">Ayam masuk, mati, afkir</p></div>
    </a>
    <a href="{{ route('produksi.create') }}" class="flex items-center space-x-3 p-4 bg-white rounded-lg shadow hover:shadow-md transition border border-gray-100">
        <div class="p-2 rounded-full bg-amber-100 text-amber-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
        <div><h4 class="font-medium text-sm text-gray-800">Input Produksi</h4><p class="text-xs text-gray-400">Panen telur harian</p></div>
    </a>
    <a href="{{ route('telur.keluar.create') }}" class="flex items-center space-x-3 p-4 bg-white rounded-lg shadow hover:shadow-md transition border border-gray-100">
        <div class="p-2 rounded-full bg-orange-100 text-orange-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
        <div><h4 class="font-medium text-sm text-gray-800">Telur Keluar</h4><p class="text-xs text-gray-400">Distribusi & penjualan</p></div>
    </a>
    @if(auth()->user()->hasRole('super_admin'))
    <a href="{{ route('penjualan.index') }}" class="flex items-center space-x-3 p-4 bg-white rounded-lg shadow hover:shadow-md transition border border-gray-100">
        <div class="p-2 rounded-full bg-emerald-100 text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><h4 class="font-medium text-sm text-gray-800">Penjualan</h4><p class="text-xs text-gray-400">Transaksi & invoice</p></div>
    </a>
    @endif
    <a href="{{ route('laporan.produksi') }}" class="flex items-center space-x-3 p-4 bg-white rounded-lg shadow hover:shadow-md transition border border-gray-100">
        <div class="p-2 rounded-full bg-indigo-100 text-indigo-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
        <div><h4 class="font-medium text-sm text-gray-800">Laporan</h4><p class="text-xs text-gray-400">Export & analisa</p></div>
    </a>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    new Chart(document.getElementById('chartProduksi'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Butir',
                data: {!! json_encode($chartProduksi) !!},
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.1)',
                fill: true, tension: 0.3, pointRadius: 3, pointBackgroundColor: '#6366f1'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => v >= 1000 ? (v/1000).toFixed(1)+'k' : v } } } }
    });

    @if(auth()->user()->hasRole('petugas_kandang') && !empty($chartPemakaianDatasets))
    new Chart(document.getElementById('chartPemakaian'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartPemakaianLabels) !!},
            datasets: {!! json_encode($chartPemakaianDatasets) !!}
        },
        options: { responsive: true, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }, plugins: { legend: { labels: { boxWidth: 12, padding: 12 } } } }
    });
    @else
    new Chart(document.getElementById('chartMortalitas'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                { label: 'Mati', data: {!! json_encode($chartMati) !!}, backgroundColor: '#ef4444', borderRadius: 4 },
                { label: 'Afkir', data: {!! json_encode($chartAfkir) !!}, backgroundColor: '#f97316', borderRadius: 4 }
            ]
        },
        options: { responsive: true, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }, plugins: { legend: { labels: { boxWidth: 12, padding: 12 } } } }
    });
    @endif
});
</script>
@endsection
