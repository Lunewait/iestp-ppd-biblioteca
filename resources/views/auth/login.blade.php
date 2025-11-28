<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Biblioteca Pedro P. Díaz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex">

    <!-- Left Side - Image & Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900 overflow-hidden">
        <img src="https://images.unsplash.com/photo-1507842217121-9e93c8aaf27c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80"
            alt="Library Background" class="absolute inset-0 w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-br from-cyan-900/90 to-blue-900/80"></div>

        <div class="relative z-10 w-full flex flex-col justify-between p-12 text-white">
            <div class="flex items-center gap-3">
                <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>
                <span class="text-2xl font-bold tracking-tight">Biblioteca</span>
            </div>

            <div class="space-y-6 mb-12">
                <h1 class="text-5xl font-bold leading-tight">
                    Tu puerta al <br> conocimiento.
                </h1>
                <p class="text-lg text-cyan-100 max-w-md leading-relaxed">
                    Accede a miles de recursos educativos, gestiona tus préstamos y descubre nuevas lecturas desde
                    cualquier lugar.
                </p>
            </div>

            <div class="text-sm text-cyan-200/60">
                &copy; {{ date('Y') }} Biblioteca Pedro P. Díaz. Todos los derechos reservados.
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center lg:text-left">
                <h2 class="text-3xl font-bold text-gray-900">Bienvenido de nuevo</h2>
                <p class="mt-2 text-gray-600">Ingresa tus credenciales para acceder a tu cuenta.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Error de autenticación</h3>
                        <p class="text-sm text-red-700 mt-1">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Institucional</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 transition sm:text-sm"
                            placeholder="estudiante@iestp.edu.pe">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 transition sm:text-sm"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Recordar dispositivo
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-cyan-600 hover:text-cyan-500">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    @endif
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition transform hover:-translate-y-0.5">
                        Iniciar Sesión
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    ¿No tienes una cuenta?
                    <a href="#" class="font-medium text-cyan-600 hover:text-cyan-500">
                        Contacta a administración
                    </a>
                </p>
            </div>
        </div>
    </div>

</body>

</html>