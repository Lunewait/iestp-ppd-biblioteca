<nav class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <span class="text-2xl font-bold text-white">📚 IESTP Library</span>
            </div>

            <!-- Menu -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('dashboard') }}" class="text-white hover:bg-blue-700 px-4 py-2 rounded transition">
                    Dashboard
                </a>
                <a href="{{ route('materials.index') }}" class="text-white hover:bg-blue-700 px-4 py-2 rounded transition">
                    📖 Materiales
                </a>
                
                @auth
                    {{-- Estudiantes ven "Mis Préstamos" y "Solicitar Préstamo" --}}
                    @if(auth()->user()->hasRole('Estudiante'))
                        <a href="{{ route('loans.index') }}" class="text-white hover:bg-blue-700 px-4 py-2 rounded transition">
                            📋 Mis Préstamos
                        </a>
                        <a href="{{ route('loan-requests.index') }}" class="text-white hover:bg-blue-700 px-4 py-2 rounded transition">
                            📝 Solicitar Préstamo
                        </a>
                    @endif
                    
                    {{-- Admin y Trabajadores ven "Préstamos" y "Aprobar Préstamos" --}}
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Jefe_Area') || auth()->user()->hasRole('Trabajador'))
                        {{-- <a href="{{ route('loans.index') }}" class="text-white hover:bg-blue-700 px-4 py-2 rounded transition">
                            📋 Préstamos
                        </a> --}}
                        <a href="{{ route('loan-approvals.index') }}" class="text-white hover:bg-blue-700 px-4 py-2 rounded transition">
                            ✅ Aprobar Préstamos
                        </a>
                    @endif
                @endauth
                
                <a href="{{ route('fines.index') }}" class="text-white hover:bg-blue-700 px-4 py-2 rounded transition">
                    💰 Multas
                </a>
                
                @auth
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Jefe_Area'))
                        <a href="{{ route('users.index') }}" class="text-white hover:bg-blue-700 px-4 py-2 rounded transition">
                            👥 Usuarios
                        </a>
                    @endif
                @endauth
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                @auth
                    <div class="relative group">
                        <button class="text-white hover:bg-blue-700 px-4 py-2 rounded flex items-center gap-2">
                            👤 {{ auth()->user()->name }}
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="hidden group-hover:block absolute right-0 mt-0 w-48 bg-white rounded-lg shadow-xl py-2 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Perfil</a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:bg-blue-700 px-4 py-2 rounded">
                        Iniciar Sesión
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button class="md:hidden text-white hover:bg-blue-700 px-3 py-2 rounded">
                ☰
            </button>
        </div>
    </div>
</nav>
