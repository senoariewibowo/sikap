<header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
    <div class="flex items-center">
        <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 focus:outline-none mr-3">
            <svg id="iconOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="text-lg font-semibold text-gray-700">@yield('page-title', 'Kasir')</h1>
    </div>

    <div class="flex items-center space-x-4">
        <div class="flex items-center bg-gray-100 rounded-full p-1">
            <form method="POST" action="{{ route('mode.switch') }}">
                @csrf
                <input type="hidden" name="mode" value="sikap">
                <button type="submit" class="px-3 py-1 text-xs font-semibold rounded-full text-gray-500 hover:text-gray-700">SIKAP</button>
            </form>
            <form method="POST" action="{{ route('mode.switch') }}">
                @csrf
                <input type="hidden" name="mode" value="kasir">
                <button type="submit" class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-600 text-white shadow cursor-default">KASIR</button>
            </form>
        </div>

        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
            {{ ucfirst(str_replace('_', ' ', Auth::user()->role->nama_role ?? 'user')) }}
        </span>

        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
