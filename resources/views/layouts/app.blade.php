<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LCB - @yield('title', 'Portal de Gestión')</title>
    @vite('resources/css/app.css')
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

    <style>
        .flatpickr-calendar svg { width: 14px !important; height: 14px !important; display: inline-block !important; }
        .flatpickr-calendar { box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; border: none !important; border-radius: 12px !important; }
        
        /* Scrollbars Premium Oscuro para el Menú */
        .dark-scrollbar::-webkit-scrollbar { width: 5px; }
        .dark-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .dark-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
        .dark-scrollbar::-webkit-scrollbar-thumb:hover { background: #52525b; }
        
        /* Scrollbar Claro para el Contenido */
        main::-webkit-scrollbar { width: 8px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        main::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden text-gray-800 antialiased">

    <div id="mobile-overlay" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <aside id="sidebar" class="w-72 bg-[#18181b] flex flex-col fixed inset-y-0 left-0 z-50 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl border-r border-zinc-800">
        
        <div class="pt-8 pb-6 px-6 flex flex-col items-center justify-center relative">
            <div class="absolute inset-0 bg-gradient-to-b from-[#4EAA68]/10 to-transparent opacity-50 pointer-events-none"></div>
            <img src="{{ asset('images/LOGOLCB_sinbanderin.png') }}" alt="Logo LCB" class="w-36 h-auto drop-shadow-[0_0_15px_rgba(78,170,104,0.3)] hover:scale-105 transition-transform duration-500 relative z-10">
            <span class="mt-4 text-[9px] uppercase tracking-[0.3em] font-black text-zinc-400 relative z-10">Portal de Gestión</span>
        </div>

        <div class="px-5 mb-6">
            <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-2xl p-3 flex items-center gap-3 backdrop-blur-sm hover:bg-zinc-800 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-zinc-700 to-zinc-900 border border-zinc-600 flex items-center justify-center text-white font-black shadow-inner">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-zinc-200 text-sm font-bold truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
                    <p class="text-[#4EAA68] text-[10px] uppercase font-black tracking-widest">{{ auth()->user()->rol ?? 'Docente' }}</p>
                </div>
            </div>
        </div>

        <nav id="sidebar-nav" class="flex-1 px-4 space-y-1 overflow-y-auto dark-scrollbar pb-6">
            
            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest px-4 mb-3 mt-2">Área Personal</p>

            <a href="{{ route('reservas.create') }}" class="relative flex items-center py-3 px-4 rounded-xl text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all duration-300 group {{ request()->routeIs('reservas.create') ? 'bg-zinc-800 text-white' : '' }}">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-[#4EAA68] rounded-r-md opacity-0 group-hover:opacity-100 transition-all duration-300 {{ request()->routeIs('reservas.create') ? 'opacity-100 shadow-[0_0_8px_#4EAA68]' : '' }}"></div>
                <span class="mr-3 text-xl transition-transform duration-300 group-hover:scale-110 grayscale opacity-70 group-hover:opacity-100 group-hover:grayscale-0 {{ request()->routeIs('reservas.create') ? 'grayscale-0 opacity-100 scale-110' : '' }}">📅</span>
                <span class="font-semibold text-sm tracking-wide">Agendar Espacio</span>
            </a>

            <a href="{{ route('reservas.index') }}" class="relative flex items-center py-3 px-4 rounded-xl text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all duration-300 group {{ request()->routeIs('reservas.index') ? 'bg-zinc-800 text-white' : '' }}">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-[#4EAA68] rounded-r-md opacity-0 group-hover:opacity-100 transition-all duration-300 {{ request()->routeIs('reservas.index') ? 'opacity-100 shadow-[0_0_8px_#4EAA68]' : '' }}"></div>
                <span class="mr-3 text-xl transition-transform duration-300 group-hover:scale-110 grayscale opacity-70 group-hover:opacity-100 group-hover:grayscale-0 {{ request()->routeIs('reservas.index') ? 'grayscale-0 opacity-100 scale-110' : '' }}">📋</span>
                <span class="font-semibold text-sm tracking-wide">Mis Solicitudes</span>
            </a>

            <a href="{{ route('servicios.transporte.create') }}" class="relative flex items-center py-3 px-4 rounded-xl text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all duration-300 group {{ request()->routeIs('servicios.transporte.*') ? 'bg-zinc-800 text-white' : '' }}">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-[#4EAA68] rounded-r-md opacity-0 group-hover:opacity-100 transition-all duration-300 {{ request()->routeIs('servicios.transporte.*') ? 'opacity-100 shadow-[0_0_8px_#4EAA68]' : '' }}"></div>
                <span class="mr-3 text-xl transition-transform duration-300 group-hover:scale-110 grayscale opacity-70 group-hover:opacity-100 group-hover:grayscale-0 {{ request()->routeIs('servicios.transporte.*') ? 'grayscale-0 opacity-100 scale-110' : '' }}">🚌</span>
                <span class="font-semibold text-sm tracking-wide">Solicitar Transporte</span>
            </a>

            <a href="{{ route('servicios.restaurante.create') }}" class="relative flex items-center py-3 px-4 rounded-xl text-zinc-400 hover:text-white hover:bg-zinc-800 transition-all duration-300 group {{ request()->routeIs('servicios.restaurante.*') ? 'bg-zinc-800 text-white' : '' }}">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-[#FFDE00] rounded-r-md opacity-0 group-hover:opacity-100 transition-all duration-300 {{ request()->routeIs('servicios.restaurante.*') ? 'opacity-100 shadow-[0_0_8px_#FFDE00]' : '' }}"></div>
                <span class="mr-3 text-xl transition-transform duration-300 group-hover:scale-110 grayscale opacity-70 group-hover:opacity-100 group-hover:grayscale-0 {{ request()->routeIs('servicios.restaurante.*') ? 'grayscale-0 opacity-100 scale-110' : '' }}">🍽️</span>
                <span class="font-semibold text-sm tracking-wide">Solicitar Restaurante</span>
            </a>

            @if(in_array(auth()->user()->rol, ['admin', 'admin_espacios', 'admin_transporte', 'gerencia_academica', 'gerencia_administrativa', 'gerencia_operativa', 'cocina']))
                <div class="h-px bg-zinc-800 my-5 w-full"></div>
                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest px-4 mb-3">Módulos Administrativos</p>
                
                <div class="space-y-2">
                    
                    @if(auth()->user()->rol === 'admin')
                        <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all text-zinc-300 hover:bg-zinc-800 {{ request()->routeIs('admin.usuarios.*') ? 'bg-zinc-700 text-white shadow-md border-l-4 border-blue-500' : '' }}">
                            <span>👥</span> Gestión de Usuarios
                        </a>
                    @endif

                    @if(in_array(auth()->user()->rol, ['admin', 'admin_espacios']))
                        <div x-data="{ open: {{ request()->routeIs('admin.reservas.*') || request()->is('espacios*') ? 'true' : 'false' }} }" class="space-y-1">
                            <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all text-zinc-300 hover:bg-zinc-800">
                                <div class="flex items-center gap-3">
                                    <span>🏢</span> Espacios y Salones
                                </div>
                                <span class="text-xs transition-transform duration-200" :class="open ? 'rotate-180' : '' ">▼</span>
                            </button>
                            <div x-show="open" class="pl-4 space-y-1" style="display: none;">
                                <a href="{{ route('admin.reservas.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold text-zinc-400 hover:bg-zinc-800 hover:text-white {{ request()->routeIs('admin.reservas.index') ? 'text-white bg-zinc-800' : '' }}">
                                    • Auditoría de Reservas
                                </a>
                                <a href="{{ route('espacios.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold text-zinc-400 hover:bg-zinc-800 hover:text-white {{ request()->is('espacios*') ? 'text-white bg-zinc-800' : '' }}">
                                    • Catálogo de Salones
                                </a>
                            </div>
                        </div>
                    @endif

                    @if(in_array(auth()->user()->rol, ['admin', 'admin_transporte']))
                        <a href="{{ route('admin.transporte.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all text-zinc-300 hover:bg-zinc-800 {{ request()->routeIs('admin.transporte.*') ? 'bg-zinc-700 text-white shadow-md border-l-4 border-[#4EAA68]' : '' }}">
                            <span>🚌</span> Auditoría Transporte
                        </a>
                    @endif

                    @if(in_array(auth()->user()->rol, ['admin', 'gerencia_academica', 'gerencia_administrativa', 'gerencia_operativa', 'cocina']))
                        <div x-data="{ open: {{ request()->routeIs('admin.restaurante.*') || request()->routeIs('cocina.*') ? 'true' : 'false' }} }" class="space-y-1">
                            <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all text-zinc-300 hover:bg-zinc-800">
                                <div class="flex items-center gap-3">
                                    <span>🍽️</span> Restaurante / Cocina
                                </div>
                                <span class="text-xs transition-transform duration-200" :class="open ? 'rotate-180' : '' ">▼</span>
                            </button>
                            <div x-show="open" class="pl-4 space-y-1" style="display: none;">
                                @if(in_array(auth()->user()->rol, ['admin', 'gerencia_academica', 'gerencia_administrativa', 'gerencia_operativa']))
                                    <a href="{{ route('admin.restaurante.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold text-zinc-400 hover:bg-zinc-800 hover:text-white {{ request()->routeIs('admin.restaurante.index') ? 'text-white bg-zinc-800' : '' }}">
                                        • Autorizar Presupuestos
                                    </a>
                                @endif
                                @if(in_array(auth()->user()->rol, ['admin', 'cocina']))
                                    <a href="{{ route('cocina.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold text-zinc-400 hover:bg-zinc-800 hover:text-white {{ request()->routeIs('cocina.index') ? 'text-white bg-zinc-800' : '' }}">
                                        • Pizarra de Producción
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            @endif
        </nav>

        <div class="p-4 border-t border-zinc-800">
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button class="w-full bg-zinc-800 hover:bg-red-500/20 border border-zinc-700 hover:border-red-500/50 text-zinc-400 hover:text-red-400 text-sm font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] relative">
        <div class="absolute inset-0 bg-slate-50/95 z-0 pointer-events-none"></div> 
        
        <header class="md:hidden bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 relative z-10 shadow-sm">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/LOGOLCB_sinbanderin.png') }}" alt="LCB" class="h-8 w-auto filter drop-shadow-sm opacity-90">
            </div>
            <button id="mobile-menu-btn" class="text-gray-600 hover:text-[#4EAA68] transition-colors focus:outline-none p-2 bg-gray-50 rounded-lg border border-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </header>

        <div class="relative z-10 w-full p-4 sm:p-8 overflow-y-auto flex-1">
            @yield('content')
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            let menuOpen = false;

            function toggleMenu() {
                menuOpen = !menuOpen;
                if (menuOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('opacity-0');
                    setTimeout(() => overlay.classList.add('hidden'), 300);
                }
            }

            mobileBtn.addEventListener('click', toggleMenu);
            overlay.addEventListener('click', toggleMenu);

            flatpickr(".calendario-lcb", { locale: "es", dateFormat: "Y-m-d", disable: [ function(date) { return (date.getDay() === 0); } ] });
            flatpickr(".reloj-lcb", { enableTime: true, noCalendar: true, dateFormat: "H:i", minTime: "07:00", maxTime: "17:00", time_24hr: false, minuteIncrement: 15 });

            // MAGIA PARA GUARDAR EL SCROLL DEL MENÚ
            const sidebarNav = document.getElementById('sidebar-nav');
            if (sidebarNav) {
                const savedScroll = localStorage.getItem('sidebarScrollPos');
                if (savedScroll) { sidebarNav.scrollTop = savedScroll; }
                window.addEventListener('beforeunload', () => {
                    localStorage.setItem('sidebarScrollPos', sidebarNav.scrollTop);
                });
            }
        });
    </script>
</body>
</html>