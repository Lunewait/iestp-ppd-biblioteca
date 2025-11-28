<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biblioteca Pedro P. Díaz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50 h-20 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div class="text-cyan-500 text-2xl">
                    <i class="fas fa-book-open"></i>
                </div>
                <span class="text-2xl font-bold text-cyan-600 tracking-tight">Biblioteca</span>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-4">
                <!-- Search Bar (Hidden on mobile) -->
                <div class="hidden md:flex relative">
                    <input type="text" placeholder="Buscar libros o recursos"
                        class="w-64 pl-4 pr-10 py-2 border border-gray-200 rounded-sm text-sm focus:outline-none focus:border-cyan-500 transition">
                    <button
                        class="absolute right-0 top-0 h-full px-3 text-cyan-500 hover:text-cyan-600 border-l border-gray-200">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- Login Button -->
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-6 py-2 bg-cyan-500 text-white text-sm font-semibold hover:bg-cyan-600 transition flex items-center gap-2">
                            Dashboard <i class="fas fa-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-6 py-2 bg-cyan-500 text-white text-sm font-semibold hover:bg-cyan-600 transition flex items-center gap-2">
                            Login <i class="fas fa-arrow-right"></i>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section with Background Image -->
    <div class="relative h-[calc(100vh-80px)] bg-gray-900 overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
                alt="Library Background" class="w-full h-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/90 to-gray-900/40"></div>
        </div>

        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
            <div class="max-w-2xl text-white space-y-8">
                <p class="text-cyan-400 font-bold tracking-wider uppercase text-sm">Sistema de Biblioteca</p>

                <h1 class="text-5xl md:text-6xl font-bold leading-tight">
                    Descubre y Disfruta de <br>
                    Miles de Libros
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed max-w-xl">
                    Accede a una amplia colección de libros, gestionados de forma rápida y eficiente desde nuestra
                    plataforma.
                    Encuentra recursos físicos y digitales para potenciar tu aprendizaje.
                </p>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="{{ route('materials.index') }}"
                        class="px-8 py-3 bg-cyan-500 text-white font-bold rounded hover:bg-cyan-600 transition shadow-lg hover:shadow-cyan-500/30">
                        Explorar Libros
                    </a>
                    <a href="{{ route('login') }}"
                        class="px-8 py-3 bg-white text-gray-900 font-bold rounded hover:bg-gray-100 transition shadow-lg">
                        Únete Ahora
                    </a>
                </div>
            </div>

            <!-- Right Side Navigation Arrows (Decorative) -->
            <div class="hidden lg:flex absolute right-8 top-1/2 -translate-y-1/2 flex-col gap-4">
                <button
                    class="w-12 h-12 border border-white/20 rounded flex items-center justify-center text-white/50 hover:text-white hover:border-white transition">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button
                    class="w-12 h-12 border border-white/20 rounded flex items-center justify-center text-white/50 hover:text-white hover:border-white transition">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

</body>

</html>