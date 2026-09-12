@extends('layouts.admin')

@section('title', 'Kasir POS - SIKAP')
@section('page-title', 'Kasir POS')
@section('main-class', 'bg-slate-100')

@section('content')
<div class="min-h-[calc(100vh-4rem)] p-4 pb-28 lg:pb-4">
    {{-- Pencarian --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <div class="relative max-w-2xl">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input id="searchInput" type="text" autocomplete="off" placeholder="Scan barcode / ketik nama produk (F2)"
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-base font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
        </div>
        <div id="resultInfo" class="hidden mt-2 text-sm font-medium text-slate-500"></div>
    </div>

    {{-- Tabel Hasil Produk --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4">
        <div class="overflow-x-auto max-h-[320px] overflow-y-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-700 uppercase bg-slate-100 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Barcode</th>
                        <th class="px-4 py-3 text-right">Harga 1</th>
                        <th class="px-4 py-3 text-right">Harga 2</th>
                        <th class="px-4 py-3 text-right">Harga 3</th>
                        <th class="px-4 py-3 text-right">Harga 4</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Ketik nama atau barcode produk</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel Keranjang --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-4">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Keranjang</h2>
            <button id="btnClear" class="px-3 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 rounded-lg text-xs font-semibold shadow-sm transition">Kosongkan</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600">
                <thead class="text-xs text-slate-700 uppercase bg-slate-100">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3 text-right">Harga 1</th>
                        <th class="px-4 py-3 text-right">Harga 2</th>
                        <th class="px-4 py-3 text-right">Harga 3</th>
                        <th class="px-4 py-3 text-right">Harga 4</th>
                        <th class="px-4 py-3 text-right">Harga Jual</th>
                        <th class="px-4 py-3 text-center w-24">Qty</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cartTableBody">
                    <tr><td colspan="10" class="px-4 py-8 text-center text-slate-400">Keranjang masih kosong</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pembayaran --}}
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-3">
                <label class="text-xs font-semibold text-slate-500 block mb-1">Nama Pembeli (opsional)</label>
                <input id="namaPembeli" type="text" placeholder="Nama pembeli"
                    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
            </div>

            <div class="md:col-span-3">
                <label class="text-xs font-semibold text-slate-500 block mb-1.5">Metode Pembayaran</label>
                <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                    <button type="button" data-method="tunai" class="method-btn py-2 rounded-lg text-sm font-bold bg-white text-emerald-700 shadow-sm transition">Tunai</button>
                    <button type="button" data-method="transfer" class="method-btn py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 transition">Transfer</button>
                </div>
            </div>

            <div class="md:col-span-6 flex flex-col items-end justify-end">
                <span class="text-sm font-semibold text-slate-500 mb-1">Total Belanja</span>
                <span id="totalDisplay" class="text-3xl font-extrabold text-emerald-600 tracking-tight">Rp 0</span>
            </div>

            <div class="md:col-span-3">
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-semibold text-slate-500" id="labelBayar">Bayar / DP</label>
                    <button id="btnUangPas" type="button" class="text-[10px] px-2 py-0.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded font-semibold transition">Uang Pas</button>
                </div>
                <input id="bayarInput" type="text" placeholder="0"
                    class="w-full text-lg font-bold border border-slate-300 rounded-xl px-3 py-2.5 text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
            </div>

            <div class="md:col-span-3">
                <label class="text-xs font-semibold text-slate-500 block mb-1" id="labelSisa">Kembalian</label>
                <div id="kembalianDisplay" class="w-full text-lg font-bold border rounded-xl px-3 py-2.5 text-emerald-600 bg-emerald-50 border-emerald-200">Rp 0</div>
            </div>

            <div class="md:col-span-6">
                <button id="btnBayar" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-bold rounded-xl text-lg shadow-lg shadow-emerald-200 transition transform active:scale-[0.98]">
                    Bayar (F4)
                </button>
            </div>

            <div class="md:col-span-12">
                <p class="text-xs text-slate-400 mt-1" id="hintBayar">Boleh kurang dari total untuk DP.</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Input Qty --}}
<div id="qtyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800">Tambah ke Keranjang</h3>
            <button type="button" onclick="closeQtyModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <p class="text-xs text-slate-500">Produk</p>
                <p id="modalProductName" class="font-bold text-slate-800"></p>
                <p id="modalProductBarcode" class="text-xs text-slate-400"></p>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Tier Harga</p>
                <div id="modalTierPrices" class="flex flex-wrap gap-2"></div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500 block mb-1">Qty</label>
                <input id="modalQty" type="number" min="1" value="1"
                    class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-lg font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
            </div>
            <div class="flex justify-between items-center bg-slate-50 rounded-lg p-3">
                <span class="text-sm font-semibold text-slate-500">Harga Jual</span>
                <span id="modalHargaJual" class="text-xl font-extrabold text-emerald-600">Rp 0</span>
            </div>
            <button id="modalTambah" type="button" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition">Tambah</button>
        </div>
    </div>
</div>

<script>
(function () {
    var products = [];
    var cart = [];
    var saveTimers = {};
    var currentMethod = 'tunai';
    var modalProduct = null;
    var selectedProductIndex = -1;

    function rupiah(n) { return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID'); }

    function priceForQty(prices, qty) {
        if (!prices || !prices.length) return 0;
        var sorted = prices.slice().sort(function (a, b) { return a.min_qty - b.min_qty; });
        var best = null;
        sorted.forEach(function (pr) {
            if (pr.min_qty <= qty && (!best || pr.min_qty > best.min_qty)) best = pr;
        });
        if (!best) best = sorted[0];
        return parseFloat(best.harga);
    }

    function tierIndexForQty(prices, qty) {
        if (!prices || !prices.length) return -1;
        var sorted = prices.slice().sort(function (a, b) { return a.min_qty - b.min_qty; });
        var best = -1, bestMin = -1;
        sorted.forEach(function (pr, i) {
            if (pr.min_qty <= qty && pr.min_qty > bestMin) { best = i; bestMin = pr.min_qty; }
        });
        if (best === -1) {
            var min = Infinity;
            sorted.forEach(function (pr, i) { if (pr.min_qty < min) { min = pr.min_qty; best = i; } });
        }
        return best;
    }

    function tierPrice(prices, tier) {
        if (!prices || !prices.length) return 0;
        var p = prices[tier - 1];
        return p ? parseFloat(p.harga) : 0;
    }

    function tierMinQty(prices, tier) {
        if (!prices || !prices.length) return '';
        var p = prices[tier - 1];
        return p ? '≥' + p.min_qty : '';
    }

    function findById(id) {
        for (var i = 0; i < products.length; i++) if (products[i].id == id) return products[i];
        return null;
    }

    function findCartIndex(productId) {
        for (var i = 0; i < cart.length; i++) if (cart[i].product_id == productId) return i;
        return -1;
    }

    function renderProductTable(list, title) {
        var tbody = document.getElementById('productTableBody');
        var info = document.getElementById('resultInfo');
        if (title) { info.textContent = title; info.classList.remove('hidden'); }
        else { info.classList.add('hidden'); }

        if (!list || !list.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Ketik nama atau barcode produk</td></tr>';
            return;
        }
        selectedProductIndex = -1;
        tbody.innerHTML = list.map(function (p, i) {
            return '<tr data-add="' + p.id + '" data-index="' + i + '" tabindex="-1" class="product-row bg-white border-b hover:bg-emerald-50 cursor-pointer transition outline-none">' +
                '<td class="px-4 py-3 font-medium text-slate-800">' + p.product_name + '</td>' +
                '<td class="px-4 py-3 text-xs">' + (p.barcode || '-') + '</td>' +
                '<td class="px-4 py-3 text-right font-semibold">' + rupiah(tierPrice(p.prices, 1)) + '</td>' +
                '<td class="px-4 py-3 text-right font-semibold">' + rupiah(tierPrice(p.prices, 2)) + '</td>' +
                '<td class="px-4 py-3 text-right font-semibold">' + rupiah(tierPrice(p.prices, 3)) + '</td>' +
                '<td class="px-4 py-3 text-right font-semibold">' + rupiah(tierPrice(p.prices, 4)) + '</td>' +
                '<td class="px-4 py-3 text-center"><button data-add-btn="' + p.id + '" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">Pilih</button></td>' +
                '</tr>';
        }).join('');
    }

    function updateSelectedRow() {
        document.querySelectorAll('.product-row').forEach(function (row, i) {
            if (i === selectedProductIndex) {
                row.classList.add('ring-2', 'ring-inset', 'ring-emerald-500', 'bg-emerald-50');
                row.setAttribute('tabindex', '0');
                row.focus();
            } else {
                row.classList.remove('ring-2', 'ring-inset', 'ring-emerald-500', 'bg-emerald-50');
                row.setAttribute('tabindex', '-1');
            }
        });
    }

    function selectProductIndex(i) {
        var rows = document.querySelectorAll('.product-row');
        if (!rows.length) return;
        if (i < 0) i = 0;
        if (i >= rows.length) i = rows.length - 1;
        selectedProductIndex = i;
        updateSelectedRow();
    }

    function openQtyModal(product) {
        modalProduct = product;
        document.getElementById('modalProductName').textContent = product.product_name;
        document.getElementById('modalProductBarcode').textContent = product.barcode || product.group_name || '';
        document.getElementById('modalQty').value = 1;

        var tierDiv = document.getElementById('modalTierPrices');
        var prices = product.prices ? product.prices.slice().sort(function (a, b) { return a.min_qty - b.min_qty; }) : [];
        tierDiv.innerHTML = prices.map(function (pr, i) {
            return '<span class="text-xs px-2 py-1 rounded-md bg-slate-100 text-slate-600 font-semibold">≥' + pr.min_qty + ' ' + rupiah(pr.harga) + '</span>';
        }).join('') || '<span class="text-xs text-slate-400">Tidak ada tier harga</span>';

        updateModalPrice();
        document.getElementById('qtyModal').classList.remove('hidden');
        setTimeout(function () { document.getElementById('modalQty').focus(); document.getElementById('modalQty').select(); }, 50);
    }

    function closeQtyModal() {
        document.getElementById('qtyModal').classList.add('hidden');
        modalProduct = null;
    }

    function updateModalPrice() {
        if (!modalProduct) return;
        var qty = parseInt(document.getElementById('modalQty').value) || 1;
        var harga = priceForQty(modalProduct.prices, qty);
        document.getElementById('modalHargaJual').textContent = rupiah(harga);
    }

    function addToCartFromModal() {
        if (!modalProduct) return;
        var qty = parseInt(document.getElementById('modalQty').value) || 1;
        addToCart(modalProduct, qty);
        closeQtyModal();
    }

    function addToCart(product, qty) {
        var idx = findCartIndex(product.id);
        var harga = priceForQty(product.prices, qty);
        if (idx >= 0) {
            var item = cart[idx];
            item.qty += qty;
            if (!item.manualHarga) item.harga = priceForQty(product.prices, item.qty);
            item.subtotal = item.harga * item.qty;
        } else {
            cart.push({
                product_id: product.id,
                name: product.product_name,
                barcode: product.barcode || '',
                group_name: product.group_name || '',
                prices: product.prices || [],
                qty: qty,
                harga: harga,
                subtotal: harga * qty,
                manualHarga: false,
                updateMaster: false
            });
        }
        renderCart();
        document.getElementById('searchInput').value = '';
        applyFilter();
        document.getElementById('searchInput').focus();
    }

    function renderCart() {
        var tbody = document.getElementById('cartTableBody');
        document.getElementById('btnClear').disabled = cart.length === 0;
        if (!cart.length) {
            tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-8 text-center text-slate-400">Keranjang masih kosong</td></tr>';
        } else {
            tbody.innerHTML = cart.map(function (item, i) {
                return '<tr class="bg-white border-b hover:bg-slate-50" data-cart-i="' + i + '">' +
                    '<td class="px-4 py-3">' + (i + 1) + '</td>' +
                    '<td class="px-4 py-3"><p class="font-medium text-slate-800">' + item.name + '</p><p class="text-xs text-slate-400">' + (item.barcode || '') + '</p></td>' +
                    '<td class="px-4 py-3 text-right font-medium">' + rupiah(tierPrice(item.prices, 1)) + '<span class="text-[10px] text-slate-400 block">' + tierMinQty(item.prices, 1) + '</span></td>' +
                    '<td class="px-4 py-3 text-right font-medium">' + rupiah(tierPrice(item.prices, 2)) + '<span class="text-[10px] text-slate-400 block">' + tierMinQty(item.prices, 2) + '</span></td>' +
                    '<td class="px-4 py-3 text-right font-medium">' + rupiah(tierPrice(item.prices, 3)) + '<span class="text-[10px] text-slate-400 block">' + tierMinQty(item.prices, 3) + '</span></td>' +
                    '<td class="px-4 py-3 text-right font-medium">' + rupiah(tierPrice(item.prices, 4)) + '<span class="text-[10px] text-slate-400 block">' + tierMinQty(item.prices, 4) + '</span></td>' +
                    '<td class="px-4 py-3 text-right"><input data-harga-input="' + i + '" type="text" value="' + Math.round(item.harga).toLocaleString('id-ID') + '" class="w-24 text-right border border-slate-300 rounded-lg px-2 py-1 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></td>' +
                    '<td class="px-4 py-3 text-center"><input data-qty-input="' + i + '" type="number" min="1" value="' + item.qty + '" class="w-16 text-center border border-slate-300 rounded-lg py-1 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"></td>' +
                    '<td class="px-4 py-3 text-right font-extrabold text-emerald-600">' + rupiah(item.subtotal) + '</td>' +
                    '<td class="px-4 py-3 text-center">' +
                        '<div class="flex items-center justify-center gap-2">' +
                            '<label class="inline-flex items-center gap-1 cursor-pointer" title="Simpan ke harga master">' +
                                '<input type="checkbox" data-master-check="' + i + '" ' + (item.updateMaster ? 'checked' : '') + ' class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">' +
                                '<span class="text-[10px] font-semibold text-indigo-600">Master</span>' +
                            '</label>' +
                            '<button data-remove="' + i + '" class="w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-base font-bold shadow-sm transition">&times;</button>' +
                        '</div>' +
                    '</td>' +
                    '</tr>';
            }).join('');
        }
        updateTotals();
    }

    function setQty(i, qty) {
        var item = cart[i];
        if (!item) return;
        item.qty = Math.max(1, qty);
        if (!item.manualHarga) {
            item.harga = priceForQty(item.prices, item.qty);
        }
        item.subtotal = item.harga * item.qty;
        renderCart();
    }

    function setHarga(i, rawValue) {
        var item = cart[i];
        if (!item) return;
        var harga = parseInt(rawValue.replace(/\D/g, '')) || 0;
        item.harga = harga;
        item.manualHarga = true;
        item.subtotal = item.harga * item.qty;
        renderCart();
        if (item.updateMaster) {
            scheduleSaveMaster(item.product_id, harga, item.qty);
        }
    }

    function scheduleSaveMaster(productId, harga, qty) {
        if (saveTimers[productId]) clearTimeout(saveTimers[productId]);
        saveTimers[productId] = setTimeout(function () {
            saveMasterPrice(productId, harga, qty);
        }, 600);
    }

    function saveMasterPrice(productId, harga, qty) {
        var p = findById(productId);
        if (!p || !p.prices || !p.prices.length) return;
        var prices = p.prices.map(function (pr) { return { min_qty: pr.min_qty, harga: pr.harga }; });
        var idx = tierIndexForQty(prices, qty || 1);
        if (idx === -1) return;
        prices[idx].harga = harga;

        fetch('{{ route('kasir.product.prices', ['product' => ':productId']) }}'.replace(':productId', productId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"').content
            },
            body: JSON.stringify({ prices: prices })
        })
        .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
        .then(function (res) {
            if (!res.ok || !res.data.success) return;
            var updated = res.data.product;
            for (var i = 0; i < products.length; i++) {
                if (products[i].id == updated.id) { products[i] = updated; break; }
            }
            cart.forEach(function (item) {
                if (item.product_id == updated.id && !item.manualHarga) {
                    item.prices = updated.prices;
                    item.harga = priceForQty(updated.prices, item.qty);
                    item.subtotal = item.harga * item.qty;
                }
            });
            renderProductTable(products, null);
            renderCart();
        });
    }

    function updateTotals() {
        var total = cart.reduce(function (s, c) { return s + c.subtotal; }, 0);
        document.getElementById('totalDisplay').textContent = rupiah(total);

        var bayarRaw = document.getElementById('bayarInput').value.replace(/\D/g, '');
        var dibayar = bayarRaw ? parseInt(bayarRaw) : 0;
        var kd = document.getElementById('kembalianDisplay');
        var labelSisa = document.getElementById('labelSisa');

        if (currentMethod === 'tunai') {
            if (dibayar >= total) {
                labelSisa.textContent = 'Kembalian';
                kd.textContent = rupiah(dibayar - total);
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2 text-emerald-600 bg-emerald-50 border-emerald-200';
            } else {
                labelSisa.textContent = 'Kekurangan';
                kd.textContent = rupiah(total - dibayar);
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2 text-amber-600 bg-amber-50 border-amber-200';
            }
        } else {
            labelSisa.textContent = 'Kekurangan';
            if (dibayar > total) {
                kd.textContent = 'Lebih dari total';
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2 text-red-600 bg-red-50 border-red-200';
            } else if (dibayar === total) {
                kd.textContent = rupiah(0);
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2 text-emerald-600 bg-emerald-50 border-emerald-200';
            } else {
                kd.textContent = rupiah(total - dibayar);
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2 text-amber-600 bg-amber-50 border-amber-200';
            }
        }
    }

    function applyFilter() {
        var q = document.getElementById('searchInput').value.trim();
        if (!q) {
            renderProductTable(null, null);
            return;
        }
        var ql = q.toLowerCase();
        var filtered = products.filter(function (p) {
            return p.product_name.toLowerCase().indexOf(ql) !== -1 || (p.barcode && p.barcode.indexOf(q) !== -1);
        });
        renderProductTable(filtered, 'Hasil: ' + filtered.length + ' produk');
    }

    function handleSearchEnter() {
        var q = document.getElementById('searchInput').value.trim();
        if (!q) return;
        var exact = products.find(function (p) { return p.barcode && p.barcode === q; });
        if (exact) {
            openQtyModal(exact);
        }
    }

    function checkout() {
        if (!cart.length) { alert('Keranjang masih kosong.'); return; }
        var total = cart.reduce(function (s, c) { return s + c.subtotal; }, 0);
        var bayarRaw = document.getElementById('bayarInput').value.replace(/\D/g, '');
        var dp = bayarRaw ? parseInt(bayarRaw) : 0;

        if (currentMethod === 'transfer' && dp > total) {
            alert('Transfer tidak boleh melebihi total.');
            return;
        }

        var btn = document.getElementById('btnBayar');
        btn.disabled = true; btn.textContent = 'Memproses...';

        fetch('{{ route('kasir.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"').content
            },
            body: JSON.stringify({
                tanggal: '{{ now()->format('Y-m-d') }}',
                metode_pembayaran: currentMethod,
                dp: dp,
                nama_pembeli: document.getElementById('namaPembeli').value.trim(),
                items: cart.map(function (c) { return { product_id: c.product_id, qty: c.qty, harga: c.harga }; })
            })
        })
        .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
        .then(function (res) {
            btn.disabled = false; btn.textContent = 'Bayar (F4)';
            if (!res.ok || !res.data.success) {
                alert(res.data.message || 'Gagal menyimpan transaksi.');
                return;
            }
            var d = res.data;
            if (d.struk_url) {
                window.open(d.struk_url + '?print=1', '_blank');
            }
            cart = [];
            document.getElementById('bayarInput').value = '';
            document.getElementById('namaPembeli').value = '';
            renderCart();
            document.getElementById('searchInput').value = '';
            applyFilter();
            document.getElementById('searchInput').focus();
        })
        .catch(function () {
            btn.disabled = false; btn.textContent = 'Bayar (F4)';
            alert('Terjadi kesalahan jaringan.');
        });
    }

    document.addEventListener('click', function (e) {
        var row = e.target.closest('[data-add]');
        var btn = e.target.closest('[data-add-btn]');
        var pid = row ? row.getAttribute('data-add') : (btn ? btn.getAttribute('data-add-btn') : null);
        if (pid) {
            if (row) {
                selectedProductIndex = parseInt(row.getAttribute('data-index'));
                updateSelectedRow();
            }
            var p = findById(pid);
            if (p) openQtyModal(p);
            return;
        }
        var removeBtn = e.target.closest('[data-remove]');
        if (removeBtn) {
            cart.splice(parseInt(removeBtn.getAttribute('data-remove')), 1);
            renderCart();
            return;
        }
        var methodBtn = e.target.closest('.method-btn');
        if (methodBtn) {
            currentMethod = methodBtn.getAttribute('data-method');
            document.querySelectorAll('.method-btn').forEach(function (b) {
                if (b.getAttribute('data-method') === currentMethod) {
                    b.className = 'method-btn py-2 rounded-lg text-sm font-bold bg-white text-emerald-700 shadow-sm transition';
                } else {
                    b.className = 'method-btn py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 transition';
                }
            });
            var labelBayar = document.getElementById('labelBayar');
            var hintBayar = document.getElementById('hintBayar');
            if (currentMethod === 'transfer') {
                labelBayar.textContent = 'Transfer / DP';
                hintBayar.textContent = 'Transfer tidak boleh melebihi total.';
            } else {
                labelBayar.textContent = 'Bayar / DP';
                hintBayar.textContent = 'Boleh kurang dari total untuk DP.';
            }
            updateTotals();
            return;
        }
    });

    document.addEventListener('input', function (e) {
        var modalQty = e.target.closest('#modalQty');
        if (modalQty) { updateModalPrice(); return; }

        var hargaInput = e.target.closest('[data-harga-input]');
        if (hargaInput) {
            var v = hargaInput.value.replace(/\D/g, '');
            hargaInput.value = v ? parseInt(v).toLocaleString('id-ID') : '';
            return;
        }

        var qtyInput = e.target.closest('[data-qty-input]');
        if (qtyInput) {
            setQty(parseInt(qtyInput.getAttribute('data-qty-input')), parseInt(qtyInput.value) || 1);
            return;
        }
    });

    document.addEventListener('blur', function (e) {
        var hargaInput = e.target.closest('[data-harga-input]');
        if (hargaInput) {
            var i = parseInt(hargaInput.getAttribute('data-harga-input'));
            setHarga(i, hargaInput.value);
        }
    }, true);

    document.addEventListener('change', function (e) {
        var masterCheck = e.target.closest('[data-master-check]');
        if (masterCheck) {
            var i = parseInt(masterCheck.getAttribute('data-master-check'));
            var item = cart[i];
            if (item) {
                item.updateMaster = masterCheck.checked;
                if (item.updateMaster && item.manualHarga) {
                    scheduleSaveMaster(item.product_id, item.harga, item.qty);
                }
            }
        }
    });

    document.getElementById('searchInput').addEventListener('input', applyFilter);
    document.getElementById('searchInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); handleSearchEnter(); }
    });
    document.getElementById('bayarInput').addEventListener('input', function () {
        var v = this.value.replace(/\D/g, '');
        this.value = v ? parseInt(v).toLocaleString('id-ID') : '';
        updateTotals();
    });
    document.getElementById('btnUangPas').addEventListener('click', function () {
        var total = cart.reduce(function (s, c) { return s + c.subtotal; }, 0);
        var input = document.getElementById('bayarInput');
        input.value = Math.round(total).toLocaleString('id-ID');
        input.dispatchEvent(new Event('input'));
        input.focus();
    });
    document.getElementById('btnClear').addEventListener('click', function () {
        if (!cart.length) return;
        if (confirm('Kosongkan keranjang?')) { cart = []; document.getElementById('bayarInput').value = ''; renderCart(); }
    });
    document.getElementById('btnBayar').addEventListener('click', checkout);
    document.getElementById('modalTambah').addEventListener('click', addToCartFromModal);
    document.getElementById('qtyModal').addEventListener('click', function (e) {
        if (e.target === this) closeQtyModal();
    });
    document.getElementById('modalQty').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); addToCartFromModal(); }
    });

    document.addEventListener('keydown', function (e) {
        var search = document.getElementById('searchInput');
        var rows = document.querySelectorAll('.product-row');
        var active = document.activeElement;
        var inModal = modalProduct !== null;
        var inInput = active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.isContentEditable);

        if (inModal) {
            if (e.key === 'Escape') { e.preventDefault(); closeQtyModal(); }
            return;
        }

        if (e.key === 'ArrowDown') {
            if (active === search || active === document.body || active === null) {
                e.preventDefault();
                selectProductIndex(0);
                return;
            }
            if (active.classList && active.classList.contains('product-row')) {
                e.preventDefault();
                selectProductIndex(selectedProductIndex + 1);
                return;
            }
        }

        if (e.key === 'ArrowUp') {
            if (active.classList && active.classList.contains('product-row')) {
                e.preventDefault();
                selectProductIndex(selectedProductIndex - 1);
                return;
            }
        }

        if (e.key === 'Enter') {
            if (active && active.classList && active.classList.contains('product-row')) {
                e.preventDefault();
                var pid = active.getAttribute('data-add');
                var p = findById(pid);
                if (p) openQtyModal(p);
                return;
            }
        }

        if (e.key === 'Escape') {
            if (active && active.classList && active.classList.contains('product-row')) {
                e.preventDefault();
                selectedProductIndex = -1;
                updateSelectedRow();
                search.focus();
                return;
            }
        }

        if (e.key === 'F2') { e.preventDefault(); search.focus(); }
        if (e.key === 'F4') { e.preventDefault(); checkout(); }
    });

    fetch('{{ route('kasir.products-json') }}', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            products = data.products || [];
            applyFilter();
        });

    renderCart();
    document.getElementById('searchInput').focus();
})();
</script>
@endsection
