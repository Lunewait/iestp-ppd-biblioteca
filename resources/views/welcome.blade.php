<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bibliotech - Sistema de Biblioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-cyan-500 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-cyan-600 tracking-tight">Bibliotech</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <div class="relative">
                        <input type="text" placeholder="Buscar libros o recursos"
                            class="w-64 pl-4 pr-10 py-2 border border-gray-200 rounded-full text-sm focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition">
                        <button class="absolute right-3 top-2.5 text-gray-400 hover:text-cyan-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>

                    @if (Route::has('login'))
                        <div class="flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="text-sm font-medium text-gray-700 hover:text-cyan-600 transition">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="px-6 py-2.5 bg-cyan-500 text-white text-sm font-semibold rounded-full hover:bg-cyan-600 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    Login ->
                                </a>
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Image Side -->
            <div class="relative order-2 lg:order-1">
                <div class="absolute -inset-4 bg-cyan-100 rounded-2xl transform -rotate-2"></div>
                <img src="{{ asset('images/hero.png') }}" alt="Estudiante en biblioteca"
                    class="relative rounded-2xl shadow-2xl w-full object-cover h-[500px] transform transition hover:scale-[1.01] duration-500">

                <!-- Floating Badge -->
                <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-xl shadow-xl flex items-center gap-3 animate-bounce"
                    style="animation-duration: 3s;">
                    <div class="bg-green-100 p-2 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase">Estado del Sistema</p>
                        <p class="text-sm font-bold text-gray-900">100% Operativo</p>
                    </div>
                </div>
            </div>

            <!-- Content Side -->
            <div class="order-1 lg:order-2 space-y-8">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 text-cyan-600 text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                    Sistema de Gestión v2.0
                </div>

                <h1 class="text-4xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                    Bienvenidos a Nuestro <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-blue-600">Sistema de
                        Biblioteca</span>
                </h1>

                <p class="text-lg text-gray-600 leading-relaxed">
                    Nuestro sistema de biblioteca está diseñado para facilitar el acceso a una amplia variedad de
                    libros. Con herramientas modernas, gestionamos el préstamo y la devolución de libros de forma
                    eficiente.
                    <br><br>
                    Creemos en el poder de la lectura para enriquecer vidas y fomentar el aprendizaje. Por eso,
                    ofrecemos un catálogo diverso, accesible tanto para estudiantes como para amantes de los libros.
                </p>

                <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-50 p-2 rounded-lg text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">Catálogo Extenso</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-purple-50 p-2 rounded-lg text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">Préstamos Simples</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-green-50 p-2 rounded-lg text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">Devoluciones Rápidas</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-orange-50 p-2 rounded-lg text-orange-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                                </path>
                            </svg>
                        </div>
                        <span class="font-medium text-gray-700">Acceso en Línea</span>
                    </div>
                </div>

                <div class="pt-4">
                    <a href="#"
                        class="px-8 py-4 bg-cyan-500 text-white font-bold rounded-lg shadow-lg hover:bg-cyan-600 transition transform hover:-translate-y-1 inline-block">
                        Más Información
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} Bibliotech. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>

</html>