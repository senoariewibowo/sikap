@extends('layouts.admin')

@section('title', 'Riwayat Kasir - SIKAP')
@section('page-title', 'Riwayat Kasir')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total Transaksi</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($totals->jumlah ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Omzet Periode</p>
        <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totals->omzet ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Lunas</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($totalsLunas ?? 0) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Belum Lunas</p>
        <p class="text-2xl font-bold text-amber-600">{{ number_format($totalsBelumLunas ?? 0) }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Transaksi</h2>
        <a href="{{ route('kasir.pos') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Buka Kasir</a>
    </div>

    <form method="GET" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500">Dari</label>
            <input type="date" name="dari" value="{{ $dari }}" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
        </div>
        <div>
            <label class="block text-xs text-gray-500">Sampai</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="border-gray-300 rounded-md text-sm px-3 py-1.5">
        </div>
        <div>
            <label class="block text-xs text-gray-500">No. Struk</label>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="STRK-..." class="border-gray-300 rounded-md text-sm px-3 py-1.5">
        </div>
        <button type="submit" class="px-4 py-1.5 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">Filter</button>
        <a href="{{ route('kasir.index') }}" class="px-3 py-1.5 text-gray-600 text-sm hover:bg-gray-200 rounded-md">Reset</a>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-3">No. Struk</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Pembeli</th>
                    <th class="px-4 py-3">Kasir</th>
                    <th class="px-4 py-3">Metode</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-right">Dibayar</th>
                    <th class="px-4 py-3 text-right">Kekurangan</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksis as $t)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $t->no_struk }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">{{ $t->nama_pembeli ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $t->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 uppercase">{{ $t->metode_pembayaran }}</td>
                    <td class="px-4 py-3">
                        @if($t->status_pembayaran === 'lunas')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Lunas</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Belum Lunas</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($t->dp, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $t->kekurangan > 0 ? 'text-amber-600' : 'text-gray-500' }}">Rp {{ number_format($t->kekurangan, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-1">
                            <a href="{{ route('kasir.struk', $t->id) }}" target="_blank" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded" title="Cetak Struk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </a>
                            @if($t->status_pembayaran === 'belum_lunas' && $t->kekurangan > 0)
                            <button type="button" onclick="openPelunasan({{ $t->id }}, {{ $t->kekurangan }}, '{{ $t->no_struk }}')" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded" title="Pelunasan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            @endif
                            <form action="{{ route('kasir.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-red-600 hover:bg-red-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="px-6 py-12 text-center text-gray-500">Belum ada transaksi kasir.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $transaksis->links() }}</div>
</div>

{{-- Modal Pelunasan --}}
<div id="pelunasanModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Pelunasan <span id="modalStruk"></span></h3>
            <button type="button" onclick="closePelunasan()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form id="pelunasanForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Kekurangan</label>
                <div id="modalKekurangan" class="text-xl font-bold text-amber-600"></div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Metode Pelunasan</label>
                <select name="metode_pembayaran" class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="tunai">Tunai</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah</label>
                <input type="number" name="jumlah" id="modalJumlah" min="1" step="0.01" required
                    class="w-full border-gray-300 rounded-lg text-lg font-bold focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Keterangan (opsional)</label>
                <input type="text" name="keterangan" placeholder="Pelunasan"
                    class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg">Simpan Pelunasan</button>
        </form>
    </div>
</div>

<script>
function openPelunasan(id, kekurangan, noStruk) {
    var form = document.getElementById('pelunasanForm');
    form.action = '/kasir/riwayat/' + id + '/pelunasan';
    document.getElementById('modalStruk').textContent = noStruk;
    document.getElementById('modalKekurangan').textContent = 'Rp ' + Math.round(kekurangan).toLocaleString('id-ID');
    document.getElementById('modalJumlah').value = kekurangan;
    document.getElementById('pelunasanModal').classList.remove('hidden');
}
function closePelunasan() {
    document.getElementById('pelunasanModal').classList.add('hidden');
}
document.getElementById('pelunasanModal').addEventListener('click', function (e) {
    if (e.target === this) closePelunasan();
});
</script>
@endsection
