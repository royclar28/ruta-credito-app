<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Ruta de Crédito' }} - Sistema de Gestión Bancaria</title>

    <!-- Tailwind CSS & Inter Font -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            800: '#1e3a8a',
                            900: '#172554',
                            950: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-800 bg-slate-50 selection:bg-brand-500 selection:text-white" x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex flex-row">
        <!-- ========================================== -->
        <!-- SIDEBAR DE NAVEGACIÓN -->
        <!-- ========================================== -->
        <aside class="w-72 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0 transition-all duration-300 z-30"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed inset-y-0 left-0 md:static">
            
            <div class="flex flex-col h-full">
                <!-- Branding Header -->
                <div class="h-20 flex items-center px-6 bg-slate-950/40 border-b border-slate-800/80 gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-brand-500/20">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <span class="text-lg font-bold tracking-tight text-white block">Ruta de Crédito</span>
                        <span class="text-xs font-medium text-slate-400 tracking-wider uppercase">Sistema Autónomo</span>
                    </div>
                </div>

                <!-- Menú Principal -->
                <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                    <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Panel Ejecutivo</div>
                    
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        <span>Dashboard de KPIs</span>
                    </a>

                    <div class="pt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Bandejas Operativas</div>

                    <a href="{{ route('workflow.index', 'carterizados') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('bandejas/carterizados*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="briefcase" class="w-5 h-5 text-amber-400"></i>
                            <span>Carterizados</span>
                        </div>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-800 text-slate-300">Activo</span>
                    </a>

                    <a href="{{ route('workflow.index', 'pyme') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('bandejas/pyme*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="store" class="w-5 h-5 text-sky-400"></i>
                            <span>Personas y PYMES</span>
                        </div>
                    </a>

                    <a href="{{ route('workflow.index', 'comite') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('bandejas/comite*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="users" class="w-5 h-5 text-purple-400"></i>
                            <span>Comité de Crédito</span>
                        </div>
                    </a>

                    <a href="{{ route('workflow.index', 'vp') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('bandejas/vp*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="award" class="w-5 h-5 text-emerald-400"></i>
                            <span>Vicepresidencia (VP)</span>
                        </div>
                    </a>

                    <a href="{{ route('workflow.index', 'aprobados') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->is('bandejas/aprobados*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400"></i>
                            <span>Aprobados / Liquidación</span>
                        </div>
                    </a>

                    <div class="pt-5 px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Gestión y Control</div>

                    <a href="{{ route('consultation.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('consultation.index') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <i data-lucide="search" class="w-5 h-5 text-indigo-400"></i>
                        <span>Consulta y Auditoría</span>
                    </a>

                    <a href="{{ route('reports.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('reports.index') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5 text-teal-400"></i>
                        <span>Centro de Reportes</span>
                    </a>
                </nav>

                <!-- Estado del Servidor & Footer -->
                <div class="p-4 border-t border-slate-800 bg-slate-950/60">
                    <div class="bg-slate-900 rounded-xl p-3 border border-slate-800/80 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs text-slate-300 font-medium">PostgreSQL 3FN</span>
                        </div>
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-800 text-slate-400">Docker</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ========================================== -->
        <!-- CONTENIDO PRINCIPAL -->
        <!-- ========================================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Navbar Superior -->
            <header class="h-20 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between sticky top-0 z-20 shadow-sm">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-500 hover:text-slate-700 p-2 rounded-lg hover:bg-slate-100">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight leading-none">{{ $headerTitle ?? 'Ruta de Crédito' }}</h1>
                        <p class="text-xs text-slate-500 mt-1 hidden sm:block">Plataforma Empresarial Desacoplada y Centralizada</p>
                    </div>
                </div>

                <!-- Perfil y Switcher Rápido de Roles para Pruebas -->
                <div class="flex items-center gap-4">
                    <!-- Dropdown de Switcher de Perfil -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 border border-slate-200 text-xs font-medium text-slate-700 transition-all shadow-sm">
                            <i data-lucide="repeat" class="w-3.5 h-3.5 text-brand-600"></i>
                            <span class="hidden lg:inline text-slate-500">Perfil:</span>
                            <span class="font-bold text-brand-700">{{ Auth::user()?->role?->name ?? 'Usuario' }}</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-72 rounded-2xl bg-white shadow-2xl border border-slate-200 py-2 z-50 animate-in fade-in zoom-in-95 duration-100">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-semibold text-slate-900">Simulador de Roles (1-Click Switch)</p>
                                <p class="text-[11px] text-slate-500">Permite testear el sistema bajo diferentes perspectivas:</p>
                            </div>
                            <div class="max-h-60 overflow-y-auto py-1">
                                @php
                                    $allUsers = \App\Models\User::with('role')->where('is_active', true)->get();
                                @endphp
                                @foreach($allUsers as $u)
                                    <a href="{{ route('switch_user', $u->id) }}" 
                                       class="flex items-center justify-between px-4 py-2 text-xs hover:bg-brand-50 transition-colors {{ Auth::id() === $u->id ? 'bg-brand-50/70 font-semibold text-brand-700' : 'text-slate-700' }}">
                                        <div>
                                            <span class="block">{{ $u->name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $u->role?->name }}</span>
                                        </div>
                                        @if(Auth::id() === $u->id)
                                            <i data-lucide="check" class="w-4 h-4 text-brand-600"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Usuario Conectado y Logout -->
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                            {{ substr(Auth::user()?->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <span class="text-xs font-bold text-slate-800 block leading-tight">{{ Auth::user()?->name }}</span>
                            <span class="text-[11px] text-slate-500">{{ Auth::user()?->email }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Cerrar Sesión" class="text-slate-400 hover:text-rose-600 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Alertas Flash -->
            @if(session('success'))
                <div class="bg-emerald-500 text-white text-sm px-6 py-2.5 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">&times;</button>
                </div>
            @endif

            <!-- Área de Contenido -->
            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Inicialización de Iconos Lucide -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
