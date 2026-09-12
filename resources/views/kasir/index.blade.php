@extends('layouts.admin')

@section('title', 'Riwayat Kasir - SIKAP')
@section('page-title', 'Riwayat Kasir')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
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
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total Kembalian</p>
        <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totalsKembalian ?? 0, 0, ',', '.') }}</p>
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
                    <th class="px-4 py-3 text-right">Kembalian</th>
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
                    <td class="px-4 py-3 text-right font-semibold {{ $t->kembalian > 0 ? 'text-emerald-600' : 'text-gray-500' }}">Rp {{ number_format($t->kembalian, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-1">
                            <a href="{{ route('kasir.struk', $t->id) }}" target="_blank" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded" title="Cetak Struk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </a>
                            <button type="button" onclick="openEditModal({{ $t->id }})" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded" title="Edit Transaksi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
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
                <tr><td colspan="11" class="px-6 py-12 text-center text-gray-500">Belum ada transaksi kasir.</td></tr>
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

{{-- Modal Edit Transaksi --}}
<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-gray-800">Edit Transaksi <span id="editModalStruk"></span></h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form id="editForm" method="POST" class="p-6 space-y-5" novalidate>
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="editTanggal" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Nama Pembeli</label>
                    <input type="text" name="nama_pembeli" id="editNamaPembeli" class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="editMetode" class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                    <select name="status_pembayaran" id="editStatus" class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="lunas">Lunas</option>
                        <option value="belum_lunas">Belum Lunas</option>
                    </select>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-semibold text-gray-500">Dibayar / DP</label>
                        <button type="button" id="btnEditUangPas" class="text-[10px] px-2 py-0.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded font-semibold transition">Uang Pas</button>
                    </div>
                    <input type="text" inputmode="numeric" name="dp" id="editDp" placeholder="0" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex items-end">
                    <div class="w-full bg-slate-50 rounded-lg p-3 border border-slate-200">
                        <p class="text-xs text-gray-500">Total</p>
                        <p id="editTotal" class="text-xl font-bold text-emerald-600">Rp 0</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-emerald-50 rounded-lg p-3 border border-emerald-100">
                    <p class="text-xs text-gray-500">Bayar</p>
                    <p id="editBayarDisplay" class="text-lg font-bold text-emerald-600">Rp 0</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                    <p class="text-xs text-gray-500" id="editSisaLabel">Kekurangan</p>
                    <p id="editSisaDisplay" class="text-lg font-bold text-amber-600">Rp 0</p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3 border border-slate-200 flex items-center">
                    <p class="text-xs text-slate-500">Transfer tidak boleh melebihi total.</p>
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-semibold text-gray-700">Item Transaksi</h4>
                    <button type="button" onclick="openAddItemModal()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">+ Tambah Item</button>
                </div>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-3 py-2">Nama Produk</th>
                                <th class="px-3 py-2 text-right">Harga Jual</th>
                                <th class="px-3 py-2 text-center w-24">Qty</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="editItemsBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Item --}}
<div id="addItemModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-gray-800">Tambah Item</h3>
            <button type="button" onclick="closeAddItemModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Cari Produk</label>
                <input type="text" id="addItemSearch" placeholder="Ketik nama produk" autocomplete="off" class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div id="addItemResults" class="max-h-40 overflow-y-auto space-y-1"></div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Qty</label>
                <input type="number" id="addItemQty" min="1" value="1" class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <button type="button" id="addItemConfirm" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition" disabled>Tambah</button>
        </div>
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

(function () {
    var editItems = [];
    var editProducts = [];
    var editId = null;
    var selectedAddProduct = null;

    function rupiah(n) { return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID'); }

    function formatInputRupiah(el) {
        var v = el.value.replace(/\D/g, '');
        el.value = v ? parseInt(v).toLocaleString('id-ID') : '';
    }

    function rawRupiah(el) {
        return parseInt((el.value || '').replace(/\D/g, '') || '0', 10);
    }

    function priceForQty(prices, qty) {
        if (!prices || !prices.length) return 0;
        var sorted = prices.slice().sort(function (a, b) { return a.min_qty - b.min_qty; });
        var best = null;
        sorted.forEach(function (pr) { if (pr.min_qty <= qty && (!best || pr.min_qty > best.min_qty)) best = pr; });
        if (!best) best = sorted[0];
        return parseFloat(best.harga);
    }

    function findProduct(id) {
        for (var i = 0; i < editProducts.length; i++) if (editProducts[i].id == id) return editProducts[i];
        return null;
    }

    function computeEditTotal() {
        var total = editItems.reduce(function (s, it) { return s + (it.harga * it.qty); }, 0);
        document.getElementById('editTotal').textContent = rupiah(total);
        var dpInput = document.getElementById('editDp');
        if (document.getElementById('editStatus').value === 'lunas' && rawRupiah(dpInput) < total) {
            dpInput.value = Math.round(total).toLocaleString('id-ID');
        }
        computeEditSummary(total);
        return total;
    }

    function computeEditSummary(total) {
        if (typeof total === 'undefined') {
            total = editItems.reduce(function (s, it) { return s + (it.harga * it.qty); }, 0);
        }
        var dp = rawRupiah(document.getElementById('editDp'));
        var metode = document.getElementById('editMetode').value;
        var status = document.getElementById('editStatus').value;

        document.getElementById('editBayarDisplay').textContent = rupiah(dp);

        var sisaLabel = document.getElementById('editSisaLabel');
        var sisaDisplay = document.getElementById('editSisaDisplay');

        if (metode === 'transfer' && dp > total) {
            sisaLabel.textContent = 'Keterangan';
            sisaDisplay.textContent = 'Bayar > total';
            sisaDisplay.className = 'text-lg font-bold text-red-600';
            return;
        }

        if (dp >= total) {
            var kembalian = metode === 'tunai' ? Math.max(0, dp - total) : 0;
            sisaLabel.textContent = 'Kembalian';
            sisaDisplay.textContent = rupiah(kembalian);
            sisaDisplay.className = 'text-lg font-bold text-emerald-600';
        } else {
            sisaLabel.textContent = 'Kekurangan';
            sisaDisplay.textContent = rupiah(total - dp);
            sisaDisplay.className = 'text-lg font-bold text-amber-600';
        }
    }

    function renderEditItems() {
        var tbody = document.getElementById('editItemsBody');
        if (!editItems.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Belum ada item</td></tr>';
        } else {
            tbody.innerHTML = editItems.map(function (it, i) {
                return '<tr class="bg-white border-b">' +
                    '<td class="px-3 py-2">' + it.product_name + '</td>' +
                    '<td class="px-3 py-2 text-right"><input data-edit-harga="' + i + '" type="text" inputmode="numeric" value="' + Math.round(it.harga).toLocaleString('id-ID') + '" class="w-24 text-right border border-gray-300 rounded px-2 py-1 text-sm"></td>' +
                    '<td class="px-3 py-2 text-center"><input data-edit-qty="' + i + '" type="number" min="1" value="' + it.qty + '" class="w-16 text-center border border-gray-300 rounded px-2 py-1 text-sm"></td>' +
                    '<td class="px-3 py-2 text-right font-semibold" data-edit-subtotal="' + i + '">' + rupiah(it.harga * it.qty) + '</td>' +
                    '<td class="px-3 py-2 text-center"><button type="button" data-edit-remove="' + i + '" class="text-red-600 hover:text-red-800 font-bold">&times;</button></td>' +
                    '</tr>';
            }).join('');
        }
        computeEditTotal();
    }

    window.openEditModal = function (id) {
        editId = id;
        fetch('/kasir/riwayat/' + id + '/edit', { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                var t = res.transaksi;
                editProducts = res.products || [];
                document.getElementById('editModalStruk').textContent = t.no_struk;
                document.getElementById('editTanggal').value = t.tanggal;
                document.getElementById('editNamaPembeli').value = t.nama_pembeli || '';
                document.getElementById('editMetode').value = t.metode_pembayaran;
                document.getElementById('editStatus').value = t.status_pembayaran;
                document.getElementById('editDp').value = Math.round(t.dp).toLocaleString('id-ID');
                editItems = t.details.map(function (d) {
                    return {
                        product_id: d.product_id,
                        product_name: d.product_name,
                        harga: d.harga,
                        qty: d.qty,
                        prices: d.prices || []
                    };
                });
                renderEditItems();
                document.getElementById('editModal').classList.remove('hidden');
            });
    };

    window.closeEditModal = function () {
        document.getElementById('editModal').classList.add('hidden');
        editId = null;
        editItems = [];
        editProducts = [];
    };

    window.openAddItemModal = function () {
        selectedAddProduct = null;
        document.getElementById('addItemSearch').value = '';
        document.getElementById('addItemQty').value = '1';
        document.getElementById('addItemResults').innerHTML = '';
        document.getElementById('addItemConfirm').disabled = true;
        document.getElementById('addItemModal').classList.remove('hidden');
        document.getElementById('addItemSearch').focus();
    };

    window.closeAddItemModal = function () {
        document.getElementById('addItemModal').classList.add('hidden');
        selectedAddProduct = null;
    };

    document.getElementById('editItemsBody').addEventListener('input', function (e) {
        var hargaInput = e.target.closest('[data-edit-harga]');
        var qtyInput = e.target.closest('[data-edit-qty]');
        if (hargaInput) {
            var i = parseInt(hargaInput.getAttribute('data-edit-harga'));
            formatInputRupiah(hargaInput);
            editItems[i].harga = rawRupiah(hargaInput);
            var row = hargaInput.closest('tr');
            row.querySelector('[data-edit-subtotal]').textContent = rupiah(editItems[i].harga * editItems[i].qty);
            computeEditTotal();
        }
        if (qtyInput) {
            var i = parseInt(qtyInput.getAttribute('data-edit-qty'));
            editItems[i].qty = parseInt(qtyInput.value) || 1;
            var row = qtyInput.closest('tr');
            row.querySelector('[data-edit-subtotal]').textContent = rupiah(editItems[i].harga * editItems[i].qty);
            computeEditTotal();
        }
    });

    document.getElementById('editItemsBody').addEventListener('click', function (e) {
        var removeBtn = e.target.closest('[data-edit-remove]');
        if (removeBtn) {
            editItems.splice(parseInt(removeBtn.getAttribute('data-edit-remove')), 1);
            renderEditItems();
        }
    });

    document.getElementById('addItemSearch').addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();
        var resDiv = document.getElementById('addItemResults');
        if (!q) { resDiv.innerHTML = ''; selectedAddProduct = null; document.getElementById('addItemConfirm').disabled = true; return; }
        var filtered = editProducts.filter(function (p) {
            return p.product_name.toLowerCase().indexOf(q) !== -1 || (p.barcode && p.barcode.indexOf(q) !== -1);
        });
        resDiv.innerHTML = filtered.map(function (p) {
            return '<button type="button" data-add-product="' + p.id + '" class="w-full text-left px-3 py-2 hover:bg-emerald-50 rounded-lg text-sm ' + (selectedAddProduct && selectedAddProduct.id == p.id ? 'bg-emerald-100' : '') + '">' +
                '<span class="font-semibold text-slate-800">' + p.product_name + '</span>' +
                '<span class="text-xs text-slate-400 ml-2">' + (p.barcode || '') + '</span>' +
                '</button>';
        }).join('');
    });

    document.getElementById('addItemResults').addEventListener('click', function (e) {
        var btn = e.target.closest('[data-add-product]');
        if (btn) {
            selectedAddProduct = findProduct(btn.getAttribute('data-add-product'));
            document.querySelectorAll('#addItemResults button').forEach(function (b) { b.classList.remove('bg-emerald-100'); });
            btn.classList.add('bg-emerald-100');
            document.getElementById('addItemConfirm').disabled = false;
        }
    });

    document.getElementById('addItemConfirm').addEventListener('click', function () {
        if (!selectedAddProduct) return;
        var qty = parseInt(document.getElementById('addItemQty').value) || 1;
        var existing = editItems.find(function (it) { return it.product_id == selectedAddProduct.id; });
        if (existing) {
            existing.qty += qty;
        } else {
            editItems.push({
                product_id: selectedAddProduct.id,
                product_name: selectedAddProduct.product_name,
                harga: priceForQty(selectedAddProduct.prices, qty),
                qty: qty,
                prices: selectedAddProduct.prices
            });
        }
        renderEditItems();
        closeAddItemModal();
    });

    document.getElementById('editDp').addEventListener('input', function () {
        formatInputRupiah(this);
        computeEditSummary();
    });

    document.getElementById('btnEditUangPas').addEventListener('click', function () {
        var total = editItems.reduce(function (s, it) { return s + (it.harga * it.qty); }, 0);
        document.getElementById('editDp').value = Math.round(total).toLocaleString('id-ID');
        computeEditSummary();
    });

    document.getElementById('editStatus').addEventListener('change', function () {
        if (this.value === 'lunas') {
            var total = editItems.reduce(function (s, it) { return s + (it.harga * it.qty); }, 0);
            document.getElementById('editDp').value = Math.round(total).toLocaleString('id-ID');
        }
        computeEditSummary();
    });

    document.getElementById('editMetode').addEventListener('change', function () {
        computeEditSummary();
    });

    document.getElementById('editForm').addEventListener('submit', function (e) {
        e.preventDefault();
        if (!editItems.length) { alert('Minimal 1 item.'); return; }
        var btn = this.querySelector('button[type="submit"]');
        btn.disabled = true; btn.textContent = 'Menyimpan...';

        fetch('/kasir/riwayat/' + editId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"').content,
                'X-HTTP-Method-Override': 'PUT'
            },
            body: JSON.stringify({
                _method: 'PUT',
                tanggal: document.getElementById('editTanggal').value,
                nama_pembeli: document.getElementById('editNamaPembeli').value,
                metode_pembayaran: document.getElementById('editMetode').value,
                status_pembayaran: document.getElementById('editStatus').value,
                dp: rawRupiah(document.getElementById('editDp')),
                items: editItems.map(function (it) { return { product_id: it.product_id, qty: it.qty, harga: it.harga }; })
            })
        })
        .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
        .then(function (res) {
            btn.disabled = false; btn.textContent = 'Simpan Perubahan';
            if (!res.ok || !res.data.success) {
                alert(res.data.message || 'Gagal menyimpan perubahan.');
                return;
            }
            closeEditModal();
            window.location.reload();
        })
        .catch(function () {
            btn.disabled = false; btn.textContent = 'Simpan Perubahan';
            alert('Terjadi kesalahan jaringan.');
        });
    });

    document.getElementById('editModal').addEventListener('click', function (e) {
        if (e.target === this) closeEditModal();
    });
    document.getElementById('addItemModal').addEventListener('click', function (e) {
        if (e.target === this) closeAddItemModal();
    });
})();
</script>
@endsection
