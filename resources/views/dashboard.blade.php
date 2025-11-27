@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Header Section with Gradient -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white shadow-xl">
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">¡Hola, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-blue-100 text-lg">Bienvenido al Sistema de Gestión Bibliotecaria del IESTP Pedro P. Díaz.</p>
            </div>
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-white opacity-10 blur-2xl"></div>
            <div class="absolute bottom-0 right-20 -mb-16 h-40 w-40 rounded-full bg-blue-400 opacity-20 blur-xl"></div>
        </div>

        <!-- Componente de Estadísticas Livewire -->
        <livewire:dashboard-stats />

        <!-- Accesos Rápidos Grid -->
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">⚡</span>
                Accesos Rápidos
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Disponible para todos -->
                <a href="{{ route('materials.index') }}"
                    class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl border border-gray-100">
                    <div
                        class="absolute right-0 top-0 h-24 w-24 translate-x-8 translate-y-[-2rem] rounded-full bg-blue-50 transition-all group-hover:bg-blue-100">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Catálogo</h3>
                        <p class="mt-1 text-sm text-gray-500">Explorar libros y recursos</p>
                    </div>
                </a>

                <!-- Solo Estudiantes -->
                @role('Estudiante')
                <a href="{{ route('loan-requests.index') }}"
                    class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl border border-gray-100">
                    <div
                        class="absolute right-0 top-0 h-24 w-24 translate-x-8 translate-y-[-2rem] rounded-full bg-emerald-50 transition-all group-hover:bg-emerald-100">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Solicitar Préstamo</h3>
                        <p class="mt-1 text-sm text-gray-500">Crear nueva solicitud</p>
                    </div>
                </a>
                @endrole

                <!-- Disponible para todos -->
                <a href="{{ route('loans.index') }}"
                    class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl border border-gray-100">
                    <div
                        class="absolute right-0 top-0 h-24 w-24 translate-x-8 translate-y-[-2rem] rounded-full bg-violet-50 transition-all group-hover:bg-violet-100">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-violet-100 text-violet-600 group-hover:bg-violet-600 group-hover:text-white transition-colors">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Mis Préstamos</h3>
                        <p class="mt-1 text-sm text-gray-500">Ver estado y devoluciones</p>
                    </div>
                </a>

                <!-- Solo Admin/Trabajador/Jefe_Area -->
                @can('approve_loan')
                    <a href="{{ route('loan-approvals.index') }}"
                        class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl border border-gray-100">
                        <div
                            class="absolute right-0 top-0 h-24 w-24 translate-x-8 translate-y-[-2rem] rounded-full bg-amber-50 transition-all group-hover:bg-amber-100">
                        </div>
                        <div class="relative z-10">
                            <div
                                class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Aprobar Préstamos</h3>
                            <p class="mt-1 text-sm text-gray-500">Gestionar solicitudes pendientes</p>
                        </div>
                    </a>
                @endcan

                <!-- Solo Admin/Jefe_Area/Trabajador (Ahora todos pueden ver, pero permissions controlan acciones) -->
                @can('view_repository')
                    <a href="{{ route('repository.index') }}"
                        class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl border border-gray-100">
                        <div
                            class="absolute right-0 top-0 h-24 w-24 translate-x-8 translate-y-[-2rem] rounded-full bg-cyan-50 transition-all group-hover:bg-cyan-100">
                        </div>
                        <div class="relative z-10">
                            <div
                                class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Repositorio</h3>
                            <p class="mt-1 text-sm text-gray-500">Tesis y documentos</p>
                        </div>
                    </a>
                @endcan

                <!-- Solo Admin -->
                @can('view_users')
                    <a href="{{ route('users.index') }}"
                        class="group relative overflow-hidden rounded-xl bg-white p-6 shadow-md transition-all hover:-translate-y-1 hover:shadow-xl border border-gray-100">
                        <div
                            class="absolute right-0 top-0 h-24 w-24 translate-x-8 translate-y-[-2rem] rounded-full bg-rose-50 transition-all group-hover:bg-rose-100">
                        </div>
                        <div class="relative z-10">
                            <div
                                class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-rose-100 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Usuarios</h3>
                            <p class="mt-1 text-sm text-gray-500">Administrar sistema</p>
                        </div>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Componentes de soporte -->
    <livewire:notification-toast />
    <livewire:export-data />
@endsection