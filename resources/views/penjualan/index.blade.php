@extends('layouts.admin')
@section('title', 'Penjualan Telur - SIKAP')
@section('page-title', 'Penjualan Telur')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Jumlah Telur</p>
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-sm text-gray-500">Total Butir</p><p class="text-xl font-bold text-gray-800">{{ number_format($totalButir) }}</p></div>
                <div><p class="text-sm text-gray-500">Total Peti</p><p class="text-xl font-bold text-gray-800">{{ number_format($totalPeti) }}</p></div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Keuangan</p>
            <div class="grid grid-cols-3 gap-4">
                <div><p class="text-sm text-gray-500">Omzet</p><p class="text-xl font-bold text-gray-800">Rp {{ number_format($omzet, 0, ',', '.') }}</p></div>
                <div><p class="text-sm text-green-500">Total DP Masuk</p><p class="text-xl font-bold text-green-700">Rp {{ number_format($totalDp, 0, ',', '.') }}</p></div>
                <div><p class="text-sm text-red-500">Piutang</p><p class="text-xl font-bold text-red-700">Rp {{ number_format($piutang, 0, ',', '.') }}</p></div>
            </div>
        </div>
    </div>
    @if($omzetPerCustomer->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden"><div class="p-4 border-b"><h3 class="text-sm font-semibold text-gray-700">Omzet & Jumlah Per Customer</h3></div>
        <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-2">Customer</th><th class="px-4 py-2">Butir</th><th class="px-4 py-2">Peti</th><th class="px-4 py-2">Omzet</th></tr></thead><tbody>
            @foreach($omzetPerCustomer as $oc)<tr class="border-b"><td class="px-4 py-2 font-medium">{{ $oc->customer->nama_customer ?? '-' }}</td><td class="px-4 py-2">{{ number_format($oc->butir) }}</td><td class="px-4 py-2">{{ number_format($oc->peti) }}</td><td class="px-4 py-2 font-semibold">Rp {{ number_format($oc->total, 0, ',', '.') }}</td></tr>@endforeach
        </tbody></table>
    </div>
    @endif
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex justify-between items-center flex-wrap gap-3"><h2 class="text-lg font-semibold text-gray-800">Daftar Transaksi</h2><div class="flex space-x-2">@if(!auth()->user()->hasRole('driver'))<a href="{{ route('penjualan.create') }}" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">&plus; Buat Penjualan</a>@endif<a href="{{ route('penjualan.excel', request()->query()) }}" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg">Excel</a><a href="{{ route('penjualan.pdf', request()->query()) }}" class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg">PDF</a></div></div>
        <form method="GET" class="p-4 border-b bg-gray-50 flex flex-wrap gap-3 items-end">
            <div><label class="text-xs text-gray-600">Customer</label><select name="customer_id" class="mt-1 border-gray-300 rounded-md text-sm"><option value="">Semua</option>@foreach($customers as $c)<option value="{{ $c->id }}" {{ $customerId==$c->id?'selected':'' }}>{{ $c->nama_customer }}</option>@endforeach</select></div>
            <div><label class="text-xs text-gray-600">Pembayaran</label><select name="status_pembayaran" class="mt-1 border-gray-300 rounded-md text-sm"><option value="">Semua</option><option value="lunas" {{ $statusBayar=='lunas'?'selected':'' }}>Lunas</option><option value="belum_lunas" {{ $statusBayar=='belum_lunas'?'selected':'' }}>Belum Lunas</option></select></div>
            <div><label class="text-xs text-gray-600">Dari</label><input type="date" name="dari" value="{{ $dari }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <div><label class="text-xs text-gray-600">Sampai</label><input type="date" name="sampai" value="{{ $sampai }}" class="mt-1 border-gray-300 rounded-md text-sm"></div>
            <button type="submit" class="px-3 py-1.5 bg-gray-700 text-white rounded-md text-sm">Filter</button><a href="{{ route('penjualan.index') }}" class="px-3 py-1.5 text-gray-600 text-sm">Reset</a>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500"><thead class="text-xs text-gray-700 uppercase bg-gray-50"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">SJ / Gudang</th><th class="px-4 py-3">Jumlah</th><th class="px-4 py-3">Butir</th><th class="px-4 py-3">Harga/Peti</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">DP</th><th class="px-4 py-3">Sisa</th><th class="px-4 py-3">Metode</th><th class="px-4 py-3">Bayar</th><th class="px-4 py-3">Invoice</th><th class="px-4 py-3">Aksi</th></tr></thead>
                <tbody>
                    @forelse($transaksis as $t)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $t->tanggal->format('d/m/Y') }}</td><td class="px-4 py-2 font-medium">{{ $t->customer->nama_customer ?? '-' }}</td>
                        <td class="px-4 py-2 text-xs">
                            @if($t->stokDetails->isNotEmpty())
                                <div class="space-y-1">
                                @foreach($t->stokDetails->unique('stok_telur_keluar_id') as $sd)
                                    <div><span class="font-medium text-gray-700">{{ $sd->stokTelurKeluar->no_referensi ?: '-' }}</span> <span class="text-gray-400">({{ $sd->stokTelurKeluar->gudang->nama_gudang ?? '-' }})</span></div>
                                @endforeach
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ number_format($t->jumlah_satuan) }} peti</td>
                        <td class="px-4 py-2">{{ number_format($t->jumlah_butir) }}</td><td class="px-4 py-2">Rp {{ number_format($t->harga_per_satuan, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 font-semibold">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 {{ $t->dp > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">{{ $t->dp > 0 ? 'Rp '.number_format($t->dp, 0, ',', '.') : '-' }}</td>
                        <td class="px-4 py-2 {{ ($t->total_harga - $t->dp) > 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">{{ ($t->total_harga - $t->dp) > 0 ? 'Rp '.number_format($t->total_harga - $t->dp, 0, ',', '.') : 'Lunas' }}</td>
                        <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ ucfirst($t->metode_pembayaran ?? 'Tunai') }}</span></td>
                        <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded-full {{ $t->status_pembayaran=='lunas'?'bg-green-100 text-green-800':'bg-red-100 text-red-800' }}">{{ $t->status_pembayaran=='lunas'?'Lunas':'Belum Lunas' }}</span></td>
                        <td class="px-4 py-2">{{ $t->no_invoice ?: '-' }}</td>
                        <td class="px-4 py-2"><div class="flex space-x-1">
                            <a href="{{ route('penjualan.invoice', $t) }}" target="_blank" class="p-1 text-blue-600 hover:bg-blue-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></a>
                            @if(!$t->ttd_petugas)<a href="{{ route('penjualan.edit', $t) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>@endif
                            @if($t->status_pembayaran=='belum_lunas')<form action="{{ route('penjualan.bayar', $t) }}" method="POST" class="inline">@csrf @method('PATCH')<input type="hidden" name="status_pembayaran" value="lunas"><input type="hidden" name="metode_pembayaran" value="{{ $t->metode_pembayaran ?? 'tunai' }}"><input type="hidden" name="catatan_pembayaran" value="{{ $t->catatan_pembayaran }}"><button class="px-2 py-1 text-xs bg-green-600 text-white rounded">Lunas</button></form>@endif
                            <form action="{{ route('penjualan.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="p-1 text-red-600 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                        </div></td>
                    </tr>
                    @empty
                    <tr><td colspan="13" class="px-4 py-12 text-center text-gray-500">Belum ada transaksi penjualan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $transaksis->links() }}</div>
    </div>
</div>
@endsection
