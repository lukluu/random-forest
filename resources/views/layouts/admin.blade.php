<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Kopi Kita</title>

    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }

        /* Scrollbar kustom agar rapi */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #F8F5F2;
        }

        ::-webkit-scrollbar-thumb {
            background: #Cbbcb3;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #8C5E3C;
        }
    </style>
</head>

<body class="bg-[#F8F5F2] flex h-screen overflow-hidden text-gray-800">

    <aside class="w-64 bg-[#5D4037] text-white flex-col hidden md:flex shadow-2xl z-20 flex-shrink-0 transition-all duration-300">

        <!-- Logo -->
        <div class="h-16 flex items-center justify-center border-b border-white/10 bg-[#4e362e]">
            <h1 class="text-xl font-bold tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-mug-hot"></i>
                KOPI KITA
            </h1>
        </div>

        <!-- Menu -->
        <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto">

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all
       {{ request()->routeIs('dashboard') ? 'bg-[#8C5E3C] text-white shadow-md' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="{{ route('dashboard.statistics') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all
       {{ request()->routeIs('dashboard.statistics') ? 'bg-[#8C5E3C] text-white shadow-md' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-chart-column w-5 text-center"></i>
                <span class="font-medium">Laporan Statistik</span>
            </a>

            <a href="{{ route('data-survey') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all
       {{ request()->routeIs('data-survey*') ? 'bg-[#8C5E3C] text-white shadow-md' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-clipboard-list w-5 text-center"></i>
                <span class="font-medium">Ulasan Pelanggan</span>
            </a>

            <a href="{{ route('admin.uji-sistem') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all
       {{ request()->routeIs('admin.uji-sistem*') ? 'bg-[#8C5E3C] text-white shadow-md' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-vial w-5 text-center"></i>
                <span class="font-medium">Uji Sistem Input</span>
            </a>
            <a href="{{ route('admin.uji-dataset.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all
       {{ request()->routeIs('admin.uji-dataset*') ? 'bg-[#8C5E3C] text-white shadow-md' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <i class="fa-solid fa-vial w-5 text-center"></i>
                <span class="font-medium">Uji Sistem Dataset</span>
            </a>

        </nav>

        <!-- User & Logout -->
        <div class="p-4 border-t border-white/10 bg-[#4e362e]">

            <div class="flex items-center gap-3 mb-3 px-2">
                <div class="w-8 h-8 rounded-full bg-[#8C5E3C] flex items-center justify-center text-xs font-bold">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-white/50 truncate">{{ Auth::user()->email ?? 'email@admin.com' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm
                       bg-red-500/10 text-red-200 hover:bg-red-600 hover:text-white rounded-lg transition-all">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </button>
            </form>

        </div>
    </aside>


    <div class="flex-1 flex flex-col h-full overflow-hidden relative">

        <header class="md:hidden h-16 bg-[#5D4037] text-white flex items-center justify-between px-4 shadow-md flex-shrink-0 z-10">
            <span class="font-bold text-lg">Kopi Kita Admin</span>
            <button class="p-2 rounded hover:bg-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth">
            @yield('content')
        </main>
    </div>

</body>

</html>