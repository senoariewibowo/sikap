@extends('layouts.admin')

@section('title', 'Detail Produksi Pakan - SIKAP')
@section('page-title', 'Detail Produksi Pakan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Detail Produksi</h2>
            <a href="{{ route('pakan.produksi.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Kembali</a>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Tanggal</p>
                <p class="font-medium text-gray-900">{{ $produksi->tanggal->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Gudang</p>
                <p class="font-medium text-gray-900">{{ $produksi->gudang->nama_gudang ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Pakan Hasil</p>
                <p class="font-medium text-gray-900">{{ $produksi->pakan->nama ?? '-' }} ({{ $produksi->pakan->satuan ?? '' }})</p>
            </div>
            <div>
                <p class="text-gray-500">Jumlah Hasil</p>
                <p class="font-medium text-gray-900">{{ number_format($produksi->jumlah, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-500">Resep</p>
                <p class="font-medium text-gray-900">{{ $produksi->resepPakan->nama_resep ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Oleh</p>
                <p class="font-medium text-gray-900">{{ $produksi->user->name ?? '-' }}</p>
            </div>
        </div>

        <div class="px-6 pb-4">
            <p class="text-gray-500 text-sm">Keterangan</p>
            <p class="font-medium text-gray-900">{{ $produksi->keterangan ?? '-' }}</p>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Bahan yang Digunakan</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-3 py-2">Bahan</th>
                            <th class="px-3 py-2 text-right">Jumlah Pakai</th>
                            <th class="px-3 py-2 text-right">Harga Satuan</th>
                            <th class="px-3 py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produksi->details as $d)
                        <tr class="bg-white border-b">
                            <td class="px-3 py-2">{{ $d->bahanPakan->nama ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($d->jumlah_pakai, 2) }} {{ $d->bahanPakan->satuan ?? '' }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($produksi->biayaLain->isNotEmpty())
        <div class="px-6 py-4 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Biaya Lain</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-3 py-2">Nama Biaya</th>
                            <th class="px-3 py-2 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produksi->biayaLain as $b)
                        <tr class="bg-white border-b">
                            <td class="px-3 py-2">{{ $b->nama_biaya }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($b->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">HPP Bahan</p>
                    <p class="font-medium text-gray-900">Rp {{ number_format($produksi->hpp_bahan, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Biaya Lain</p>
                    <p class="font-medium text-gray-900">Rp {{ number_format($produksi->biaya_lain, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">HPP Total / Satuan</p>
                    <p class="font-medium text-gray-900">Rp {{ number_format($produksi->hpp_total, 0, ',', '.') }} / Rp {{ number_format($produksi->hpp_per_satuan, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
