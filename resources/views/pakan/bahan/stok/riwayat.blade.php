@extends('layouts.admin')

@section('title', 'Riwayat Stok Bahan Pakan - SIKAP')
@section('page-title', 'Riwayat Stok Bahan Pakan')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Stok</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $stok->bahanPakan->nama }} - {{ $stok->gudang->nama_gudang }}</p>
        </div>
        <a href="{{ route('pakan.bahan.stok.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Kembali</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-3 py-3">Tanggal</th>
                    <th class="px-3 py-3 text-right">Jumlah Lama</th>
                    <th class="px-3 py-3 text-right">Perubahan</th>
                    <th class="px-3 py-3 text-right">Total</th>
                    <th class="px-3 py-3">Keterangan</th>
                    <th class="px-3 py-3">Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $log->tanggal->format('d M Y') }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($log->jumlah_lama, 2) }}</td>
                    <td class="px-3 py-2 text-right {{ $log->jumlah_baru >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ ($log->jumlah_baru >= 0 ? '+' : '') . number_format($log->jumlah_baru, 2) }}
                    </td>
                    <td class="px-3 py-2 text-right">{{ number_format($log->total, 2) }}</td>
                    <td class="px-3 py-2">{{ $log->keterangan ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $log->user->name ?? '-' }}</td>
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
