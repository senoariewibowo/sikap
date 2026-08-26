@extends('layouts.admin')

@section('title', 'Riwayat Stok - SIKAP')
@section('page-title', 'Riwayat Update Stok')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Riwayat: {{ $stok->pakan->nama ?? '-' }}
            </h2>
            <p class="text-sm text-gray-500">Gudang: {{ $stok->gudang->nama_gudang ?? '-' }} — Stok saat ini: {{ number_format($stok->jumlah, 1) }} {{ $stok->pakan->satuan ?? '' }}</p>
        </div>
        <a href="{{ route('pakan.stok.index') }}" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Kembali</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-3 py-3">Tanggal</th>
                    <th class="px-3 py-3 text-right">Stok Lama</th>
                    <th class="px-3 py-3 text-right text-green-600">+ Tambah</th>
                    <th class="px-3 py-3 text-right font-semibold">Total</th>
                    <th class="px-3 py-3">Keterangan</th>
                    <th class="px-3 py-3">Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="bg-white border-b">
                    <td class="px-3 py-2">{{ $log->tanggal->format('d/m/Y') }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($log->jumlah_lama, 1) }}</td>
                    <td class="px-3 py-2 text-right text-green-600">+{{ number_format($log->jumlah_baru, 1) }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-gray-900">{{ number_format($log->total, 1) }}</td>
                    <td class="px-3 py-2 text-gray-400">{{ $log->keterangan ?: '-' }}</td>
                    <td class="px-3 py-2 text-gray-400">{{ $log->user->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada riwayat.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">{{ $logs->links() }}</div>
</div>
@endsection
