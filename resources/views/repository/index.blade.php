@extends('layouts.app')

@section('title', 'Repositorio Digital')

@section('content')
    <div class="space-y-8">
        <!-- Header Section -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-cyan-600 to-blue-700 p-8 text-white shadow-xl">
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Repositorio Digital</h1>
                    <p class="text-cyan-100 text-lg">Accede a tesis, investigaciones y trabajos académicos.</p>
                </div>
                @can('submit_document')
                    <a href="{{ route('repository.create') }}"
                        class="group inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-700 rounded-xl font-bold hover:bg-blue-50 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Subir Documento
                    </a>
                @endcan
            </div>
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-white opacity-10 blur-2xl"></div>
            <div class="absolute bottom-0 left-20 -mb-16 h-40 w-40 rounded-full bg-cyan-400 opacity-20 blur-xl"></div>
        </div>

        <!-- Search & Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form action="{{ route('repository.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-6 lg:col-span-8 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" placeholder="Buscar por título, autor o palabras clave..."
                        value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-gray-50 focus:bg-white">
                </div>
                <div class="md:col-span-3 lg:col-span-2">
                    <select name="tipo"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-gray-50 focus:bg-white cursor-pointer">
                        <option value="">Todos los tipos</option>
                        <option value="tesis" {{ request('tipo') === 'tesis' ? 'selected' : '' }}>Tesis</option>
                        <option value="investigacion" {{ request('tipo') === 'investigacion' ? 'selected' : '' }}>
                            Investigación</option>
                        <option value="trabajo_final" {{ request('tipo') === 'trabajo_final' ? 'selected' : '' }}>Trabajo
                            Final</option>
                    </select>
                </div>
                <div class="md:col-span-3 lg:col-span-2">
                    <button type="submit"
                        class="w-full px-6 py-3 bg-gray-900 text-white rounded-xl font-semibold hover:bg-gray-800 transition shadow-md">
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($documentos as $doc)
                <div
                    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col overflow-hidden hover:-translate-y-1">
                    <!-- Card Header / Type Indicator -->
                    <div
                        class="h-2 w-full {{ $doc->tipo === 'tesis' ? 'bg-purple-500' : ($doc->tipo === 'investigacion' ? 'bg-blue-500' : 'bg-emerald-500') }}">
                    </div>

                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $doc->tipo === 'tesis' ? 'bg-purple-100 text-purple-800' : ($doc->tipo === 'investigacion' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $doc->tipo)) }}
                            </span>

                            @if($doc->status !== 'publicado')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $doc->status === 'pendiente' ? 'bg-amber-100 text-amber-800' : ($doc->status === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($doc->status) }}
                                </span>
                            @endif
                        </div>

                        <h3
                            class="text-xl font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                            {{ $doc->titulo }}
                        </h3>

                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $doc->autor }}
                        </div>

                        <p class="text-gray-600 text-sm mb-6 line-clamp-3 flex-1">
                            {{ $doc->descripcion }}
                        </p>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-auto">
                            <div class="flex items-center text-gray-400 text-xs">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                {{ $doc->descargas }}
                            </div>

                            <div class="flex gap-2">
                                @can('approve_document')
                                    @if($doc->status === 'pendiente' || $doc->status === 'aprobado')
                                        <a href="{{ route('repository.approve', $doc) }}"
                                            class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition"
                                            title="Gestionar Aprobación">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                @endcan

                                <a href="{{ route('repository.show', $doc) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-50 text-gray-700 rounded-lg text-sm font-medium hover:bg-blue-50 hover:text-blue-700 transition">
                                    Ver Detalles
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full flex flex-col items-center justify-center py-16 px-4 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No se encontraron documentos</h3>
                    <p class="text-gray-500 text-center max-w-md mb-8">
                        No hay documentos que coincidan con tu búsqueda. Intenta ajustar los filtros o busca con otros términos.
                    </p>
                    <a href="{{ route('repository.index') }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                        Limpiar filtros
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $documentos->links() }}
        </div>
    </div>
@endsection