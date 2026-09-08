@extends('layouts.admin')

@section('title', 'Dashboard Kasir - SIKAP')
@section('page-title', 'Dashboard Kasir')

@section('content')
<div class="space-y-6">
    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow p-5">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Dari</label>
                <input type="date" name="dari" value="{{ $dari }}" class="border-gray-300 rounded-lg text-sm px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Sampai</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="border-gray-300 rounded-lg text-sm px-3 py-2">
            </div>
            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">Tampilkan</button>
            <a href="{{ route('kasir.dashboard') }}" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg text-sm font-semibold">Reset</a>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-emerald-500">
            <p class="text-xs font-semibold text-gray-500 uppercase">Omzet</p>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($summary->omzet, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-indigo-500">
            <p class="text-xs font-semibold text-gray-500 uppercase">Keuntungan</p>
            <p class="text-2xl font-extrabold text-indigo-600 mt-1">Rp {{ number_format($profit, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
            <p class="text-xs font-semibold text-gray-500 uppercase">Transaksi</p>
            <p class="text-2xl font-extrabold text-blue-600 mt-1">{{ number_format($summary->total_transaksi) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ number_format($summary->lunas) }} lunas / {{ number_format($summary->belum_lunas) }} belum</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
            <p class="text-xs font-semibold text-gray-500 uppercase">Kekurangan</p>
            <p class="text-2xl font-extrabold text-amber-600 mt-1">Rp {{ number_format($summary->kekurangan, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Chart --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Penjualan & Keuntungan Harian</h2>
        <div class="relative h-80 w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Table Daily --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Rincian Harian</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3 text-right">Omzet</th>
                        <th class="px-5 py-3 text-right">Keuntungan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($labels as $i => $label)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $label }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-emerald-600">Rp {{ number_format($omzetPerHari[$i], 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-indigo-600">Rp {{ number_format($profitPerHari[$i], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    var ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [
                {
                    label: 'Omzet',
                    data: {!! json_encode($omzetPerHari) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                },
                {
                    label: 'Keuntungan',
                    data: {!! json_encode($profitPerHari) !!},
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': Rp ' + parseInt(context.raw).toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) { return 'Rp ' + parseInt(value).toLocaleString('id-ID'); }
                    }
                }
            }
        }
    });
})();
</script>
@endsection
