@extends('layouts.admin')

@section('title', 'Kasir POS - SIKAP')
@section('page-title', 'Kasir')
@section('main-class', '')

@section('content')
<div class="h-[calc(100vh-4rem)] flex flex-col lg:flex-row bg-slate-100">
    {{-- Area Produk --}}
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Header pencarian --}}
        <div class="bg-white border-b border-slate-200 px-4 py-3 shadow-sm shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative flex-1 max-w-2xl">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input id="searchInput" type="text" autocomplete="off" placeholder="Scan barcode / ketik nama produk (F2)"
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-base font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                </div>
                <div id="resultInfo" class="hidden whitespace-nowrap text-sm font-medium text-slate-500 bg-slate-100 px-3 py-2 rounded-lg"></div>
            </div>
        </div>

        {{-- Grid Produk --}}
        <div id="productArea" class="flex-1 overflow-y-auto p-4">
            <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4"></div>
        </div>
    </main>

    {{-- Keranjang --}}
    <aside id="cartAside" class="w-full lg:w-[440px] bg-white border-l border-slate-200 shadow-2xl flex flex-col shrink-0 z-20 transition-all duration-300 ease-in-out overflow-hidden relative">
        {{-- Tampilan Expanded --}}
        <div id="cartExpanded" class="flex flex-col h-full w-full">
            {{-- Header Keranjang --}}
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <button id="btnToggleCart" type="button" class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Sembunyikan keranjang">
                        <svg id="iconToggleCart" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Keranjang</h2>
                        <p class="text-xs text-slate-500" id="cartItemCount">0 item</p>
                    </div>
                </div>
                <button id="btnClear" class="px-3 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 rounded-lg text-xs font-semibold shadow-sm transition">Kosongkan</button>
            </div>

            {{-- Daftar Item --}}
            <div id="cartItems" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/50"></div>

            {{-- Footer Total --}}
            <div class="p-5 border-t border-slate-200 bg-white space-y-4 shrink-0">
                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1">Nama Pembeli (opsional)</label>
                    <input id="namaPembeli" type="text" placeholder="Nama pembeli"
                        class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 block mb-1.5">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                        <button type="button" data-method="tunai" class="method-btn py-2 rounded-lg text-sm font-bold bg-white text-emerald-700 shadow-sm transition">Tunai</button>
                        <button type="button" data-method="transfer" class="method-btn py-2 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 transition">Transfer</button>
                    </div>
                </div>

                <div class="flex justify-between items-end">
                    <span class="text-sm font-semibold text-slate-500">Total Belanja</span>
                    <span id="totalDisplay" class="text-4xl font-extrabold text-emerald-600 tracking-tight break-all text-right">Rp 0</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold text-slate-500" id="labelBayar">Bayar / DP</label>
                            <button id="btnUangPas" type="button" class="text-[10px] px-2 py-0.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded font-semibold transition">Uang Pas</button>
                        </div>
                        <input id="bayarInput" type="text" placeholder="0"
                            class="w-full text-lg font-bold border border-slate-300 rounded-xl px-3 py-2.5 text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        <p class="text-xs text-slate-400 mt-1" id="hintBayar">Boleh kurang dari total untuk DP.</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-500 block mb-1" id="labelSisa">Kembalian</label>
                        <div id="kembalianDisplay" class="w-full text-lg font-bold text-slate-700 bg-slate-100 border border-slate-200 rounded-xl px-3 py-2.5">Rp 0</div>
                    </div>
                </div>

                <button id="btnBayar" class="w-full py-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-bold rounded-xl text-xl shadow-lg shadow-emerald-200 transition transform active:scale-[0.98]">
                    Bayar (F4)
                </button>
            </div>
        </div>

        {{-- Tampilan Collapsed Strip --}}
        <div id="cartCollapsed" class="hidden w-14 h-full flex-col items-center py-3 bg-slate-50 border-l border-slate-200 select-none">
            <button id="btnExpandCart" type="button" class="relative p-2 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 transition mb-2" title="Tampilkan keranjang">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span id="collapsedCartCountBadge" class="hidden absolute -top-0.5 -right-0.5 bg-emerald-600 text-white text-[10px] font-bold min-w-[18px] h-[18px] flex items-center justify-center rounded-full px-1">0</span>
            </button>
            <div class="flex-1 flex items-center justify-center">
                <span class="text-xs font-semibold text-slate-400 tracking-widest" style="writing-mode: vertical-rl; transform: rotate(180deg);">KERANJANG</span>
            </div>
        </div>
    </aside>

    {{-- Floating button untuk mobile saat cart collapse --}}
    <button id="btnShowCart" type="button" class="hidden lg:hidden fixed bottom-4 right-4 z-40 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-xl px-4 py-3 items-center gap-2 transition transform hover:scale-105">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
        <span class="font-bold" id="showCartTotal">Rp 0</span>
        <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full" id="showCartCount">0</span>
    </button>
</div>

<script>
(function () {
    var products = [];
    var cart = [];
    var saveTimers = {};
    var currentMethod = 'tunai';
    var cartVisible = true;

    function rupiah(n) { return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID'); }

    function isMobileView() {
        return window.innerWidth < 1024;
    }

    function applyCartVisibility() {
        var aside = document.getElementById('cartAside');
        var expanded = document.getElementById('cartExpanded');
        var collapsed = document.getElementById('cartCollapsed');
        var floatBtn = document.getElementById('btnShowCart');
        var btn = document.getElementById('btnToggleCart');
        var icon = document.getElementById('iconToggleCart');
        var mobile = isMobileView();

        if (cartVisible) {
            aside.classList.remove('hidden', 'w-14');
            aside.classList.add('w-full', 'lg:w-[440px]');
            expanded.classList.remove('hidden');
            collapsed.classList.add('hidden');
            if (floatBtn) floatBtn.classList.add('hidden');
            if (btn) btn.title = 'Sembunyikan keranjang';
            if (icon) icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>';
        } else {
            expanded.classList.add('hidden');
            if (mobile) {
                aside.classList.add('hidden');
                aside.classList.remove('w-14', 'w-full', 'lg:w-[440px]');
                collapsed.classList.add('hidden');
                if (floatBtn) floatBtn.classList.remove('hidden');
            } else {
                aside.classList.remove('hidden', 'w-full', 'lg:w-[440px]');
                aside.classList.add('w-14');
                collapsed.classList.remove('hidden');
                if (floatBtn) floatBtn.classList.add('hidden');
            }
        }
        updateCollapsedSummary();
        try { localStorage.setItem('pos_cart_hidden', cartVisible ? '0' : '1'); } catch (e) {}
    }

    function updateCollapsedSummary() {
        var total = cart.reduce(function (s, c) { return s + c.subtotal; }, 0);
        var badge = document.getElementById('collapsedCartCountBadge');
        var floatTotal = document.getElementById('showCartTotal');
        var floatCount = document.getElementById('showCartCount');
        if (badge) {
            badge.textContent = cart.length;
            badge.classList.toggle('hidden', cart.length === 0);
        }
        if (floatTotal) floatTotal.textContent = rupiah(total);
        if (floatCount) floatCount.textContent = cart.length;
    }

    function priceForQty(prices, qty) {
        if (!prices || !prices.length) return 0;
        var best = null;
        prices.forEach(function (pr) {
            if (pr.min_qty <= qty && (!best || pr.min_qty > best.min_qty)) best = pr;
        });
        if (!best) {
            var smallest = prices[0];
            prices.forEach(function (pr) { if (pr.min_qty < smallest.min_qty) smallest = pr; });
            best = smallest;
        }
        return parseFloat(best.harga);
    }

    function tierIndexForQty(prices, qty) {
        if (!prices || !prices.length) return -1;
        var best = -1, bestMin = -1;
        prices.forEach(function (pr, i) {
            if (pr.min_qty <= qty && pr.min_qty > bestMin) { best = i; bestMin = pr.min_qty; }
        });
        if (best === -1) {
            var min = Infinity;
            prices.forEach(function (pr, i) { if (pr.min_qty < min) { min = pr.min_qty; best = i; } });
        }
        return best;
    }

    function findById(id) {
        for (var i = 0; i < products.length; i++) if (products[i].id == id) return products[i];
        return null;
    }

    function addToCart(product, qty) {
        qty = qty || 1;
        var item = cart.find(function (c) { return c.product_id == product.id; });
        if (item) {
            item.qty += qty;
            if (!item.manualHarga) {
                item.harga = priceForQty(product.prices, item.qty);
            }
        } else {
            item = {
                product_id: product.id,
                name: product.product_name,
                barcode: product.barcode || '',
                group_name: product.group_name || '',
                qty: qty,
                harga: priceForQty(product.prices, qty),
                manualHarga: false,
                updateMaster: false
            };
            cart.push(item);
        }
        item.subtotal = item.harga * item.qty;
        renderCart();
        flashCart();
    }

    function flashCart() {
        var el = document.getElementById('cartAside');
        el.classList.add('ring-2', 'ring-emerald-400');
        setTimeout(function () { el.classList.remove('ring-2', 'ring-emerald-400'); }, 150);
    }

    function renderTierButtons(item, i) {
        var p = findById(item.product_id);
        var prices = p && p.prices ? p.prices.slice().sort(function (a, b) { return a.min_qty - b.min_qty; }) : [];
        if (!prices.length) return '';
        var activeIdx = item.manualHarga ? -1 : tierIndexForQty(prices, item.qty);
        return '<div class="mt-2">' +
            '<p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Tier Harga</p>' +
            '<div class="flex flex-wrap gap-1.5">' +
            prices.map(function (pr, idx) {
                var active = idx === activeIdx;
                return '<button data-tier="' + idx + '" data-cart-i="' + i + '" class="text-xs px-2.5 py-1 rounded-full border font-semibold transition ' + (active ? 'bg-emerald-600 text-white border-emerald-600 shadow' : 'bg-white text-slate-600 border-slate-300 hover:border-emerald-400 hover:text-emerald-700') + '">' +
                    '≥' + pr.min_qty + ' ' + rupiah(pr.harga) +
                    '</button>';
            }).join('') +
            '</div></div>';
    }

    function renderCart() {
        var el = document.getElementById('cartItems');
        document.getElementById('cartItemCount').textContent = cart.length + ' item' + (cart.length > 1 ? 's' : '');
        updateCollapsedSummary();
        if (!cart.length) {
            el.innerHTML = '<div class="h-full flex flex-col items-center justify-center text-slate-400 py-10"><svg class="w-14 h-14 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg><p class="text-sm font-medium">Keranjang masih kosong</p><p class="text-xs">Klik produk untuk menambahkan</p></div>';
        } else {
            el.innerHTML = cart.map(function (item, i) {
                return '<div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm" data-cart-i="' + i + '">' +
                    '<div class="flex justify-between items-start gap-3">' +
                    '<div class="min-w-0">' +
                    '<p class="text-sm font-bold text-slate-800 truncate" title="' + item.name + '">' + item.name + '</p>' +
                    '<p class="text-xs text-slate-400 truncate">' + (item.barcode || item.group_name || '&nbsp;') + '</p>' +
                    '</div>' +
                    '<button data-remove="' + i + '" class="w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-base font-bold shadow-sm transition">&times;</button>' +
                    '</div>' +
                    renderTierButtons(item, i) +
                    '<div class="mt-3">' +
                    '<label class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Harga Satuan</label>' +
                    '<div class="flex gap-2 items-center mt-1">' +
                    '<input data-harga-input="' + i + '" type="text" value="' + Math.round(item.harga).toLocaleString('id-ID') + '" class="flex-1 border border-slate-300 rounded-lg px-3 py-1.5 text-sm font-bold text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">' +
                    '</div>' +
                    '<label class="inline-flex items-center gap-1.5 mt-2 cursor-pointer select-none">' +
                    '<input type="checkbox" data-master-check="' + i + '" ' + (item.updateMaster ? 'checked' : '') + ' class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">' +
                    '<span class="text-xs font-semibold text-indigo-600">Simpan juga ke harga master</span>' +
                    '</label>' +
                    '</div>' +
                    '<div class="flex items-center justify-between mt-4">' +
                    '<div class="flex items-center gap-2">' +
                    '<button data-qty="-1" data-i="' + i + '" class="w-9 h-9 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow font-bold text-lg flex items-center justify-center transition">&minus;</button>' +
                    '<input data-qty-input="' + i + '" type="number" min="1" value="' + item.qty + '" class="w-14 text-center border border-slate-300 rounded-lg py-1.5 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">' +
                    '<button data-qty="1" data-i="' + i + '" class="w-9 h-9 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow font-bold text-lg flex items-center justify-center transition">+</button>' +
                    '</div>' +
                    '<span class="text-lg font-extrabold text-emerald-600">' + rupiah(item.subtotal) + '</span>' +
                    '</div></div>';
            }).join('');
        }
        updateTotals();
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
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2.5 text-emerald-600 bg-emerald-50 border-emerald-200';
            } else {
                labelSisa.textContent = 'Kekurangan';
                kd.textContent = rupiah(total - dibayar);
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2.5 text-amber-600 bg-amber-50 border-amber-200';
            }
        } else {
            labelSisa.textContent = 'Kekurangan';
            if (dibayar > total) {
                kd.textContent = 'Lebih dari total';
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2.5 text-red-600 bg-red-50 border-red-200';
            } else if (dibayar === total) {
                kd.textContent = rupiah(0);
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2.5 text-emerald-600 bg-emerald-50 border-emerald-200';
            } else {
                kd.textContent = rupiah(total - dibayar);
                kd.className = 'w-full text-lg font-bold border rounded-xl px-3 py-2.5 text-amber-600 bg-amber-50 border-amber-200';
            }
        }
        updateCollapsedSummary();
    }

    function setQty(i, qty) {
        var item = cart[i];
        if (!item) return;
        item.qty = Math.max(1, qty);
        var p = findById(item.product_id);
        if (!item.manualHarga && p) {
            item.harga = priceForQty(p.prices, item.qty);
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
                    item.harga = priceForQty(updated.prices, item.qty);
                    item.subtotal = item.harga * item.qty;
                }
            });
            renderCart();
        });
    }

    function renderProducts(list, title) {
        var grid = document.getElementById('productGrid');
        var info = document.getElementById('resultInfo');

        if (title) { info.textContent = title; info.classList.remove('hidden'); }
        else { info.classList.add('hidden'); }

        if (!list || !list.length) {
            grid.innerHTML = '<div class="col-span-full flex flex-col items-center justify-center text-slate-400 py-16"><svg class="w-16 h-16 mb-4 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><p class="text-base font-medium text-slate-500">Ketik nama atau barcode produk</p><p class="text-sm">Produk akan muncul di sini</p></div>';
            return;
        }
        grid.innerHTML = list.map(function (p) {
            var price = (p.prices && p.prices.length) ? rupiah(priceForQty(p.prices, 1)) : '-';
            return '<button data-add="' + p.id + '" class="group bg-white border border-slate-200 rounded-xl p-4 text-left hover:shadow-md hover:border-emerald-400 active:scale-[0.98] transition flex flex-col h-full relative overflow-hidden">' +
                '<div class="absolute top-0 left-0 w-1 h-full bg-emerald-500 opacity-0 group-hover:opacity-100 transition"></div>' +
                '<div class="flex-1"><p class="text-sm font-bold text-slate-800 leading-tight line-clamp-2 mb-1" title="' + p.product_name + '">' + p.product_name + '</p>' +
                '<p class="text-xs text-slate-400 truncate">' + (p.barcode || (p.group_name ? p.group_name : '&nbsp;')) + '</p></div>' +
                '<div class="mt-3 flex items-center justify-between">' +
                '<span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-sm font-extrabold">' + price + '</span>' +
                '<span class="text-xs font-semibold text-emerald-600 opacity-0 group-hover:opacity-100 transition">+ Tambah</span>' +
                '</div></button>';
        }).join('');
    }

    function applyFilter() {
        var q = document.getElementById('searchInput').value.trim();
        if (!q) {
            renderProducts(null, null);
            return;
        }
        var ql = q.toLowerCase();
        var filtered = products.filter(function (p) {
            return p.product_name.toLowerCase().indexOf(ql) !== -1 || (p.barcode && p.barcode.indexOf(q) !== -1);
        });
        renderProducts(filtered, 'Hasil: ' + filtered.length + ' produk');
    }

    function handleSearchEnter() {
        var q = document.getElementById('searchInput').value.trim();
        if (!q) return;
        var exact = products.find(function (p) { return p.barcode && p.barcode === q; });
        if (exact) {
            addToCart(exact, 1);
            document.getElementById('searchInput').value = '';
            applyFilter();
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
        var addBtn = e.target.closest('[data-add]');
        if (addBtn) {
            var p = findById(addBtn.getAttribute('data-add'));
            if (p) addToCart(p, 1);
            return;
        }
        var removeBtn = e.target.closest('[data-remove]');
        if (removeBtn) {
            cart.splice(parseInt(removeBtn.getAttribute('data-remove')), 1);
            renderCart();
            return;
        }
        var qtyBtn = e.target.closest('[data-qty]');
        if (qtyBtn) {
            var i = parseInt(qtyBtn.getAttribute('data-i'));
            var delta = parseInt(qtyBtn.getAttribute('data-qty'));
            setQty(i, cart[i].qty + delta);
            return;
        }
        var tierBtn = e.target.closest('[data-tier]');
        if (tierBtn) {
            var ci = parseInt(tierBtn.getAttribute('data-cart-i'));
            var tierIdx = parseInt(tierBtn.getAttribute('data-tier'));
            var item = cart[ci];
            var p = findById(item.product_id);
            if (item && p && p.prices && p.prices[tierIdx]) {
                var prices = p.prices.slice().sort(function (a, b) { return a.min_qty - b.min_qty; });
                item.harga = parseFloat(prices[tierIdx].harga);
                item.manualHarga = false;
                item.subtotal = item.harga * item.qty;
                renderCart();
            }
            return;
        }
    });

    document.addEventListener('change', function (e) {
        var input = e.target.closest('[data-qty-input]');
        if (input) setQty(parseInt(input.getAttribute('data-qty-input')), parseInt(input.value) || 1);

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

    document.addEventListener('input', function (e) {
        var hargaInput = e.target.closest('[data-harga-input]');
        if (hargaInput) {
            var v = hargaInput.value.replace(/\D/g, '');
            hargaInput.value = v ? parseInt(v).toLocaleString('id-ID') : '';
        }
    });

    document.addEventListener('blur', function (e) {
        var hargaInput = e.target.closest('[data-harga-input]');
        if (hargaInput) {
            var i = parseInt(hargaInput.getAttribute('data-harga-input'));
            setHarga(i, hargaInput.value);
        }
    }, true);

    document.getElementById('searchInput').addEventListener('input', applyFilter);
    document.getElementById('searchInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); handleSearchEnter(); }
    });
    document.getElementById('bayarInput').addEventListener('input', function () {
        var v = this.value.replace(/\D/g, '');
        this.value = v ? parseInt(v).toLocaleString('id-ID') : '';
        updateTotals();
    });

    document.querySelectorAll('.method-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentMethod = this.getAttribute('data-method');
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
        });
    });
    document.getElementById('btnClear').addEventListener('click', function () {
        if (!cart.length) return;
        if (confirm('Kosongkan keranjang?')) { cart = []; document.getElementById('bayarInput').value = ''; renderCart(); }
    });
    document.getElementById('btnBayar').addEventListener('click', checkout);

    document.getElementById('btnToggleCart').addEventListener('click', function () {
        cartVisible = false;
        applyCartVisibility();
    });
    document.getElementById('btnUangPas').addEventListener('click', function () {
        var total = cart.reduce(function (s, c) { return s + c.subtotal; }, 0);
        var input = document.getElementById('bayarInput');
        input.value = Math.round(total).toLocaleString('id-ID');
        input.dispatchEvent(new Event('input'));
        input.focus();
    });
    document.getElementById('btnExpandCart').addEventListener('click', function () {
        cartVisible = true;
        applyCartVisibility();
    });
    document.getElementById('btnShowCart').addEventListener('click', function () {
        cartVisible = true;
        applyCartVisibility();
    });
    window.addEventListener('resize', function () {
        applyCartVisibility();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'F2') { e.preventDefault(); document.getElementById('searchInput').focus(); }
        if (e.key === 'F4') { e.preventDefault(); checkout(); }
    });

    fetch('{{ route('kasir.products-json') }}', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            products = data.products || [];
            applyFilter();
        });

    try { cartVisible = localStorage.getItem('pos_cart_hidden') !== '1'; } catch (e) {}
    applyCartVisibility();
    renderCart();
    document.getElementById('searchInput').focus();
})();
</script>
@endsection
