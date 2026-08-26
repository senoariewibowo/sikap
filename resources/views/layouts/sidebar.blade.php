<aside class="fixed inset-y-0 left-0 bg-white shadow-lg z-50 transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out flex flex-col" id="sidebar">
    <div class="flex items-center justify-center h-16 border-b border-gray-200 shrink-0">
        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
            <span class="text-indigo-600 logo-text">SIKAP</span>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto mt-4 px-4 pb-6 space-y-1">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="nav-text">Dashboard</span>
        </x-sidebar-link>

        @if(auth()->user()->hasRole('super_admin'))
        <div class="pt-4"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Master Data</p></div>

        <x-sidebar-link :href="route('kandang.index')" :active="request()->routeIs('kandang.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="nav-text">Kandang</span>
        </x-sidebar-link>
        <x-sidebar-link :href="route('karyawan.index')" :active="request()->routeIs('karyawan.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
            <span class="nav-text">Karyawan</span>
        </x-sidebar-link>
        <x-sidebar-link :href="route('gudang.index')" :active="request()->routeIs('gudang.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="nav-text">Gudang</span>
        </x-sidebar-link>
        @endif

        @if(!auth()->user()->hasRole('driver'))
        <div class="pt-4"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Operasional</p></div>

        @if(!auth()->user()->hasRole('petugas_gudang'))
        <x-sidebar-link :href="route('populasi.index')" :active="request()->routeIs('populasi.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            <span class="nav-text">Populasi Ayam</span>
        </x-sidebar-link>
        <x-sidebar-link :href="route('produksi.index')" :active="request()->routeIs('produksi.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="nav-text">Produksi Telur</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasAnyRole(['super_admin', 'petugas_gudang']))
        <x-sidebar-link :href="route('setoran.review')" :active="request()->routeIs('setoran.review')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span class="nav-text">Review Setoran</span>
        </x-sidebar-link>
        <x-sidebar-link :href="route('setoran.gudang')" :active="request()->routeIs('setoran.gudang*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="nav-text">Stok Gudang</span>
        </x-sidebar-link>
        @endif

        @endif

        @if(!auth()->user()->hasRole('driver'))
        <div class="pt-4"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Pakan</p></div>

        @if(auth()->user()->hasRole('super_admin'))
        <x-sidebar-link :href="route('pakan.index')" :active="request()->routeIs('pakan.index') || request()->routeIs('pakan.create') || request()->routeIs('pakan.edit')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="nav-text">Master Pakan</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasAnyRole(['super_admin', 'petugas_gudang']))
        <x-sidebar-link :href="route('pakan.stok.index')" :active="request()->routeIs('pakan.stok.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="nav-text">Stok Pakan</span>
        </x-sidebar-link>
        <x-sidebar-link :href="route('pakan.distribusi.index')" :active="request()->routeIs('pakan.distribusi.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <span class="nav-text">Distribusi Pakan</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasRole('petugas_kandang'))
        <x-sidebar-link :href="route('pakan.pemakaian.index')" :active="request()->routeIs('pakan.pemakaian.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="nav-text">Pemakaian Pakan</span>
        </x-sidebar-link>
        @endif
        @endif

        @if(!auth()->user()->hasRole('driver'))
        <div class="pt-4"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Obat & Vitamin</p></div>

        @if(auth()->user()->hasRole('super_admin'))
        <x-sidebar-link :href="route('obat.index')" :active="request()->routeIs('obat.index') || request()->routeIs('obat.create') || request()->routeIs('obat.edit')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <span class="nav-text">Master Obat</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasAnyRole(['super_admin', 'petugas_gudang']))
        <x-sidebar-link :href="route('obat.stok.index')" :active="request()->routeIs('obat.stok.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="nav-text">Stok Obat & Vitamin</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasRole('petugas_kandang'))
        <x-sidebar-link :href="route('obat.pemakaian.index')" :active="request()->routeIs('obat.pemakaian.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m-6 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span class="nav-text">Pemakaian Obat</span>
        </x-sidebar-link>
        @endif
        @endif

        <div class="pt-4"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Stok & Penjualan Telur</p></div>

        @if(!auth()->user()->hasRole('driver'))
        <x-sidebar-link :href="route('telur.keluar.index')" :active="request()->routeIs('telur.keluar.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM18 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 17h-1a1 1 0 01-1-1v-3a1 1 0 011-1h1m0 5h10m4 0h1a1 1 0 001-1v-2.586a1 1 0 00-.293-.707l-2.414-2.414A1 1 0 0016.586 8H14m0 9V6a1 1 0 00-1-1H5a1 1 0 00-1 1v10"/></svg>
            <span class="nav-text">Telur Keluar</span>
        </x-sidebar-link>
        @endif

        @if(!auth()->user()->hasAnyRole(['petugas_kandang', 'driver']))
        <x-sidebar-link :href="route('eceran.transaksi.index')" :active="request()->routeIs('eceran.transaksi.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            <span class="nav-text">Transaksi Eceran</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasAnyRole(['super_admin', 'driver']))
        <x-sidebar-link :href="route('penjualan.index')" :active="request()->routeIs('penjualan.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="nav-text">Penjualan</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasRole('driver'))
        <x-sidebar-link :href="route('transaksi.index')" :active="request()->routeIs('transaksi.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span class="nav-text">Transaksi</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasRole('super_admin'))
        <x-sidebar-link :href="route('customer.index')" :active="request()->routeIs('customer.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span class="nav-text">Customer</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasRole('super_admin'))
        <x-sidebar-link :href="route('harga.index')" :active="request()->routeIs('harga.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <span class="nav-text">Harga Telur</span>
        </x-sidebar-link>
        @endif

        @if(!auth()->user()->hasRole('driver'))
        <div class="pt-4"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Laporan & Peta</p></div>

        <x-sidebar-link :href="route('laporan.produksi')" :active="request()->routeIs('laporan.produksi*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="nav-text">Laporan Produksi</span>
        </x-sidebar-link>
        <x-sidebar-link :href="route('laporan.mortalitas')" :active="request()->routeIs('laporan.mortalitas*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
            <span class="nav-text">Laporan Mortalitas</span>
        </x-sidebar-link>
        <x-sidebar-link :href="route('laporan.pakan')" :active="request()->routeIs('laporan.pakan*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            <span class="nav-text">Laporan Pakan</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('peta.index')" :active="request()->routeIs('peta.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="nav-text">Peta Kandang</span>
        </x-sidebar-link>
        @endif

        @if(auth()->user()->hasRole('super_admin'))
        <div class="pt-4"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Keuangan</p></div>
        <x-sidebar-link :href="route('keuangan.pengeluaran.index')" :active="request()->routeIs('keuangan.pengeluaran.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="nav-text">Pengeluaran</span>
        </x-sidebar-link>
        <x-sidebar-link :href="route('keuangan.kategori.index')" :active="request()->routeIs('keuangan.kategori.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <span class="nav-text">Kategori Pengeluaran</span>
        </x-sidebar-link>
        @endif
    </nav>
</aside>
