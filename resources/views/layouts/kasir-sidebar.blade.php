<aside class="fixed inset-y-0 left-0 bg-white shadow-lg z-50 transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out flex flex-col" id="sidebar">
    <div class="flex items-center justify-center h-16 border-b border-gray-200 shrink-0">
        <a href="{{ route('kasir.pos') }}" class="text-xl font-bold text-gray-800">
            <span class="text-emerald-600 logo-text">KASIR</span>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto mt-4 px-4 pb-6 space-y-1">
        <x-sidebar-link :href="route('kasir.dashboard')" :active="request()->routeIs('kasir.dashboard')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6m0 0h2m2 0h2a2 2 0 002-2v-6m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="nav-text">Dashboard Kasir</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('kasir.pos')" :active="request()->routeIs('kasir.pos')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            <span class="nav-text">Transaksi</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('kasir.index')" :active="request()->routeIs('kasir.index') || request()->routeIs('kasir.struk')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span class="nav-text">Riwayat Transaksi</span>
        </x-sidebar-link>

        <div class="pt-4"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Master Produk</p></div>

        <x-sidebar-link :href="route('kasir.group.index')" :active="request()->routeIs('kasir.group.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span class="nav-text">Grup Produk</span>
        </x-sidebar-link>

        <x-sidebar-link :href="route('kasir.product.index')" :active="request()->routeIs('kasir.product.*')">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="nav-text">Produk</span>
        </x-sidebar-link>

        <div class="pt-4"><p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Aplikasi</p></div>

        <form method="POST" action="{{ route('mode.switch') }}" class="px-2">
            @csrf
            <input type="hidden" name="mode" value="sikap">
            <button type="submit" class="w-full flex items-center px-3 py-2 rounded-md text-sm text-indigo-700 hover:bg-indigo-50">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                <span class="nav-text">Kembali ke SIKAP</span>
            </button>
        </form>
    </nav>
</aside>
