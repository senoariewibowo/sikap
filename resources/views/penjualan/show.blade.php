@extends('layouts.admin')
@section('title', 'Detail Penjualan - SIKAP')
@section('page-title', 'Detail Transaksi Penjualan')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Transaksi #{{ $transaksi->id }}</h2>
            <a href="{{ route('penjualan.index') }}" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50">Kembali</a>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-gray-500">Tanggal</p><p class="font-medium">{{ $transaksi->tanggal->format('d/m/Y') }}</p></div>
                <div><p class="text-gray-500">No. Invoice</p><p class="font-medium">{{ $transaksi->no_invoice ?: '-' }}</p></div>
                <div><p class="text-gray-500">Customer</p><p class="font-medium">{{ $transaksi->customer->nama_customer ?? '-' }}</p></div>
                <div><p class="text-gray-500">Tipe Customer</p><p>{{ ucfirst($transaksi->customer->tipe_customer ?? '') }}</p></div>
                <div><p class="text-gray-500">Satuan</p><p>{{ str_replace('_', ' ', $transaksi->satuan) }}</p></div>
                <div><p class="text-gray-500">Jumlah</p><p>{{ number_format($transaksi->jumlah_satuan) }} {{ str_replace('per_', '', $transaksi->satuan) }}</p></div>
                <div><p class="text-gray-500">Setara Butir / Berat</p><p>{{ number_format($transaksi->jumlah_butir) }} butir / {{ number_format($transaksi->berat_kg, 1) }} kg</p></div>
                <div><p class="text-gray-500">Harga per Satuan</p><p>Rp {{ number_format($transaksi->harga_per_satuan, 0, ',', '.') }}</p></div>
                <div><p class="text-gray-500">Total Harga</p><p class="font-bold text-lg">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p></div>
                <div><p class="text-gray-500">DP / Uang Muka</p><p class="font-medium {{ $transaksi->dp > 0 ? 'text-green-600' : 'text-gray-400' }}">Rp {{ number_format($transaksi->dp, 0, ',', '.') }}</p></div>
                <div><p class="text-gray-500">Sisa Tagihan</p><p class="font-bold {{ ($transaksi->total_harga - $transaksi->dp) > 0 ? 'text-red-600' : 'text-green-600' }}">{{ ($transaksi->total_harga - $transaksi->dp) > 0 ? 'Rp ' . number_format($transaksi->total_harga - $transaksi->dp, 0, ',', '.') : 'Lunas' }}</p></div>
                <div><p class="text-gray-500">Status Pembayaran</p>
                    <span class="px-2 py-1 text-xs rounded-full {{ $transaksi->status_pembayaran=='lunas'?'bg-green-100 text-green-800':'bg-red-100 text-red-800' }}">{{ $transaksi->status_pembayaran=='lunas'?'Lunas':'Belum Lunas' }}</span>
                </div>
                <div><p class="text-gray-500">Input Oleh</p><p>{{ $transaksi->user->name ?? '-' }}</p></div>
            </div>

            @if($transaksi->stokDetails && $transaksi->stokDetails->isNotEmpty())
            <div class="pt-4 border-t">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Sumber Stok</h3>
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr><th class="px-3 py-1">Kandang</th><th class="px-3 py-1 text-right">Butir</th><th class="px-3 py-1 text-right">Berat</th><th class="px-3 py-1">No. SJ</th></tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi->stokDetails as $sd)
                        <tr class="border-b">
                            <td class="px-3 py-1">{{ $sd->stokTelurKeluar->gudang->nama_gudang ?? '-' }}</td>
                            <td class="px-3 py-1 text-right">{{ number_format($sd->jumlah_butir) }}</td>
                            <td class="px-3 py-1 text-right">{{ number_format($sd->berat_kg, 1) }} kg</td>
                            <td class="px-3 py-1 text-xs font-mono">{{ $sd->stokTelurKeluar->no_referensi ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @if($transaksi->status_pembayaran == 'belum_lunas')
            <form action="{{ route('penjualan.bayar', $transaksi) }}" method="POST" class="pt-4 border-t">
                @csrf @method('PATCH')
                <input type="hidden" name="status_pembayaran" value="lunas">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">Tandai Lunas</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
