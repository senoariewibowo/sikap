<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'SIKAP'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="h-screen bg-gray-100 flex overflow-hidden">
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden');"></div>

        @include('layouts.sidebar')

        <div id="appContent" class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out">
            @include('layouts.topbar')

            <main class="flex-1 p-6 overflow-y-auto">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <style>
        #sidebar { width: 16rem; }
        #sidebar.collapsed { width: 3.5rem; }
        #appContent { margin-left: 16rem; }
        @media (min-width: 1024px) {
            #sidebar.collapsed ~ #appContent { margin-left: 3.5rem !important; }
        }
        @media (max-width: 1023px) {
            #appContent { margin-left: 0 !important; }
        }
        #sidebar.collapsed .logo-text,
        #sidebar.collapsed .nav-text {
            display: none;
        }
        #sidebar.collapsed a {
            justify-content: center;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        #sidebar.collapsed nav { padding-left: 0.25rem !important; padding-right: 0.25rem !important; }
        #sidebar.collapsed a svg { margin-right: 0 !important; flex-shrink: 0; }

        #sidebar.collapsed:hover { width: 16rem; }
        #sidebar.collapsed:hover .logo-text,
        #sidebar.collapsed:hover .nav-text { display: inline !important; }
        #sidebar.collapsed:hover a { justify-content: flex-start !important; }
        #sidebar.collapsed:hover a svg { margin-right: 0.75rem !important; }
        #sidebar.collapsed:hover nav { padding-left: 1rem !important; padding-right: 1rem !important; }
        @media (min-width: 1024px) {
            #sidebar.collapsed:hover ~ #appContent { margin-left: 16rem !important; }
        }
    </style>

    @stack('scripts')
    <script>
        document.addEventListener('input', function(e) {
            if (e.target.dataset.type !== 'rupiah') return;
            var val = e.target.value.replace(/\D/g, '');
            e.target.value = val ? parseInt(val).toLocaleString('id-ID') : '';
        });
        document.addEventListener('focusin', function(e) {
            if (e.target.dataset.type !== 'rupiah') return;
            e.target.value = e.target.value.replace(/\D/g, '');
        });
        document.addEventListener('focusout', function(e) {
            if (e.target.dataset.type !== 'rupiah') return;
            var val = e.target.value.replace(/\D/g, '');
            e.target.value = val ? parseInt(val).toLocaleString('id-ID') : '';
        });
        document.addEventListener('submit', function(e) {
            e.target.querySelectorAll('[data-type="rupiah"]').forEach(function(el) {
                el.value = el.value.replace(/\D/g, '');
            });
        });

        document.getElementById('sidebarToggle').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth >= 1024) {
                sidebar.classList.toggle('collapsed');
            } else {
                sidebar.classList.toggle('-translate-x-full');
                if (sidebar.classList.contains('-translate-x-full')) {
                    overlay.classList.add('hidden');
                } else {
                    sidebar.classList.remove('hidden');
                    overlay.classList.remove('hidden');
                }
            }
        });
    </script>
</body>
</html>
