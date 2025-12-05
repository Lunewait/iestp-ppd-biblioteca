<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Biblioteca Pedro P. Díaz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
</head>

<body class="bg-gray-50 font-sans antialiased">
    @auth
        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Trabajador') || auth()->user()->hasRole('Jefe_Area'))
            <div class="flex min-h-screen">
                <!-- Sidebar for Admin/Worker -->
                <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-xl fixed h-full z-30">
                    <!-- Logo -->
                    <div class="h-16 flex items-center justify-center border-b border-slate-800 bg-slate-950">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold flex items-center gap-2 text-blue-400">
                            <i class="fas fa-book-reader"></i>
                            <span>IESTP Panel</span>
                        </a>
                    </div>

                    <!-- User Info -->
                    <div class="p-4 border-b border-slate-800 bg-slate-900/50">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-400">{{ auth()->user()->roles->pluck('name')->join(', ') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Principal</p>

                        @unless(auth()->user()->hasRole('Jefe_Area'))
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300' }} transition-colors">
                                <i class="fas fa-chart-pie w-5 text-center"></i> Dashboard
                            </a>
                        @endunless

                        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Gestión</p>

                        <a href="{{ route('materials.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('materials.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }} transition-colors">
                            <i class="fas fa-book w-5 text-center"></i> Catálogo
                        </a>

                        @can('approve_loan')
                            <a href="{{ route('loan-approvals.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('loan-approvals.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }} transition-colors">
                                <i class="fas fa-check-circle w-5 text-center"></i> Aprobar Préstamos
                            </a>
                        @endcan

                        @can('view_loans')
                            <a href="{{ route('loans.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('loans.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }} transition-colors">
                                <i class="fas fa-history w-5 text-center"></i> Historial Préstamos
                            </a>
                        @endcan

                        <a href="{{ route('repository.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('repository.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }} transition-colors">
                            <i class="fas fa-archive w-5 text-center"></i> Repositorio
                        </a>

                        @can('view_fines')
                            <a href="{{ route('fines.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('fines.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }} transition-colors">
                                <i class="fas fa-money-bill-wave w-5 text-center"></i> Multas
                            </a>
                        @endcan

                        @can('view_users')
                            <a href="{{ route('users.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }} transition-colors">
                                <i class="fas fa-users w-5 text-center"></i> Usuarios
                            </a>
                        @endcan

                        @if(auth()->user()->hasRole('Admin'))
                            <a href="{{ route('reports.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }} transition-colors">
                                <i class="fas fa-chart-bar w-5 text-center"></i> Reportes
                            </a>
                        @endif
                    </nav>

                    <!-- Footer Actions -->
                    <div class="p-4 border-t border-slate-800">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg hover:bg-red-900/30 text-red-400 hover:text-red-300 transition-colors">
                                <i class="fas fa-sign-out-alt w-5 text-center"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Main Content Wrapper -->
                <div class="flex-1 ml-64 flex flex-col min-h-screen">
                    <!-- Top Bar -->
                    <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 sticky top-0 z-20">
                        <h2 class="text-xl font-bold text-gray-800">
                            @yield('title', 'Panel de Administración')
                        </h2>
                        <div class="flex items-center gap-4">
                            <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fas fa-bell"></i>
                            </button>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <span class="text-sm text-gray-500">{{ now()->format('d/m/Y') }}</span>
                        </div>
                    </header>
                    @php
                        /** @var \Illuminate\Support\ViewErrorBag $errors */
                    @endphp
                    <!-- Page Content -->
                    <main class="flex-1 p-8 overflow-y-auto">
                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-exclamation-circle text-red-500"></i>
                                    <h3 class="font-semibold text-red-800">Atención requerida</h3>
                                </div>
                                <ul class="mt-2 list-disc list-inside text-sm text-red-700 ml-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div
                                class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm flex items-center gap-3">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <p class="text-green-800 font-medium">{{ session('success') }}</p>
                            </div>
                        @endif

                        @if (session('error'))
                            <div
                                class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm flex items-center gap-3">
                                <i class="fas fa-times-circle text-red-500"></i>
                                <p class="text-red-800 font-medium">{{ session('error') }}</p>
                            </div>
                        @endif

                        @yield('content')
                    </main>

                    <!-- Footer -->
                    <footer class="bg-white border-t border-gray-200 py-4 px-8 text-center text-sm text-gray-500">
                        &copy; {{ date('Y') }} Biblioteca Pedro P. Díaz - Panel Administrativo
                    </footer>
                </div>
            </div>
        @else
            <!-- Layout for Students/Others (Horizontal Navbar) -->
            <div class="min-h-screen flex flex-col">
                <nav class="bg-gradient-to-r from-blue-900 to-blue-800 text-white shadow-lg sticky top-0 z-50">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center h-16">
                            <div class="flex items-center gap-8">
                                <a href="{{ route('materials.index') }}"
                                    class="text-xl font-bold flex items-center gap-2 tracking-tight">
                                    <div class="bg-white/10 p-1.5 rounded-lg">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    Biblioteca
                                </a>

                                <div class="hidden md:flex gap-1">
                                    <a href="{{ route('materials.index') }}"
                                        class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white/10 transition-colors {{ request()->routeIs('materials.*') ? 'bg-white/20' : '' }}">
                                        Catálogo
                                    </a>

                                    @role('Estudiante')
                                    <a href="{{ route('loan-requests.index') }}"
                                        class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white/10 transition-colors {{ request()->routeIs('loan-requests.*') ? 'bg-white/20' : '' }}">
                                        Solicitar
                                    </a>
                                    <a href="{{ route('loans.index') }}"
                                        class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white/10 transition-colors {{ request()->routeIs('loans.*') ? 'bg-white/20' : '' }}">
                                        Historial
                                    </a>
                                    @endrole

                                    @can('approve_loan')
                                        <a href="{{ route('loan-approvals.index') }}"
                                            class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white/10 transition-colors {{ request()->routeIs('loan-approvals.*') ? 'bg-white/20' : '' }}">
                                            Aprobar
                                        </a>
                                    @endcan

                                    <a href="{{ route('repository.index') }}"
                                        class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white/10 transition-colors {{ request()->routeIs('repository.*') ? 'bg-white/20' : '' }}">
                                        Repositorio
                                    </a>

                                    @can('view_fines')
                                        <a href="{{ route('fines.index') }}"
                                            class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white/10 transition-colors {{ request()->routeIs('fines.*') ? 'bg-white/20' : '' }}">
                                            Multas
                                        </a>
                                    @endcan

                                    @can('view_users')
                                        <a href="{{ route('users.index') }}"
                                            class="px-3 py-2 rounded-md text-sm font-medium hover:bg-white/10 transition-colors {{ request()->routeIs('users.*') ? 'bg-white/20' : '' }}">
                                            Usuarios
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div
                                    class="flex items-center gap-3 bg-blue-950/30 px-3 py-1.5 rounded-full border border-blue-700/50">
                                    <div
                                        class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-xs font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium pr-1">{{ auth()->user()->name }}</span>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-200 hover:text-white transition-colors"
                                        title="Cerrar Sesión">
                                        <i class="fas fa-sign-out-alt text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </nav>
                @php
                    /** @var \Illuminate\Support\ViewErrorBag $errors */
                @endphp
                <!-- Main Content -->
                <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                            <h3 class="font-semibold text-red-800 mb-2">Errores:</h3>
                            <ul class="list-disc list-inside text-red-700 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
                            <p class="text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                            <p class="text-red-800 font-medium">{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- Alertas para estudiantes con restricciones --}}
                    @if(auth()->user()->hasRole('Estudiante'))
                        @php
                            $overdueLoans = \App\Models\Prestamo::where('user_id', auth()->id())
                                ->where('status', 'activo')
                                ->where('approval_status', 'collected')
                                ->where('fecha_devolucion_esperada', '<', now())
                                ->count();

                            $pendingFines = \App\Models\Multa::where('user_id', auth()->id())
                                ->where('status', 'pendiente')
                                ->sum('monto');
                        @endphp

                        @if($overdueLoans > 0)
                            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-600 rounded-r-lg shadow-sm">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">⚠️</span>
                                    <div>
                                        <h4 class="font-bold text-red-800">¡Préstamo Vencido!</h4>
                                        <p class="text-red-700">Tienes {{ $overdueLoans }} préstamo(s) vencido(s). <strong>Devuelve los
                                                libros a la biblioteca inmediatamente.</strong></p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($pendingFines > 0)
                            <div class="mb-6 p-4 bg-amber-100 border-l-4 border-amber-600 rounded-r-lg shadow-sm">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">💰</span>
                                    <div>
                                        <h4 class="font-bold text-amber-800">Multas Pendientes</h4>
                                        <p class="text-amber-700">Tienes S/. {{ number_format($pendingFines, 2) }} en multas.
                                            <strong>Acércate a la biblioteca para pagar.</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @yield('content')
                </main>

                <!-- Footer -->
                <footer class="bg-white border-t border-gray-200 mt-auto">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <div class="text-center text-sm text-gray-500">
                            <p>&copy; 2025 Biblioteca Pedro P. Díaz. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </footer>
            </div>
        @endif
    @else
        <!-- Guest Layout (Login/Register pages usually extend guest layout, but if they extend app) -->
        <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
            <div class="w-full max-w-md">
                @yield('content')
            </div>
        </div>
    @endauth

    <!-- Notifications -->
    <livewire:notification-toast />

    @livewireScripts
</body>

</html>