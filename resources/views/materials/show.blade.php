@extends('layouts.app')

@section('title', $material->title)

@section('content')
    <div class="mb-6">
        <a href="{{ route('materials.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            ← Volver al Catálogo
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Material Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold mb-2 text-gray-900">{{ $material->title }}</h1>
                        <p class="text-gray-600 text-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $material->author }}
                        </p>
                    </div>
                    <span
                        class="px-4 py-2 text-sm font-bold rounded-full
                                {{ $material->type === 'fisico' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ $material->type === 'fisico' ? '📚 Físico' : '💻 Digital' }}
                    </span>
                </div>

                <p class="text-gray-600 mb-4">{{ $material->description }}</p>

                <div class="border-t border-gray-100 pt-4">
                    <p class="mb-2"><strong>Código:</strong> {{ $material->code }}</p>
                    @if($material->keywords)
                        <p class="mb-2"><strong>Palabras clave:</strong> {{ $material->keywords }}</p>
                    @endif
                </div>
            </div>

            <!-- Physical Material Info -->
            @if($material->materialFisico)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">📚 Información Física</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">ISBN</p>
                            <p class="text-lg font-semibold">{{ $material->materialFisico->isbn ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Editorial</p>
                            <p class="text-lg font-semibold">{{ $material->materialFisico->publisher ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Año de Publicación</p>
                            <p class="text-lg font-semibold">{{ $material->materialFisico->publication_year ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Ubicación</p>
                            <p class="text-lg font-semibold">{{ $material->materialFisico->location ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Digital Material Info -->
            @if($material->materialDigital)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">💻 Información Digital</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm">Tipo de Archivo</p>
                            <p class="text-lg font-semibold">{{ strtoupper($material->materialDigital->file_type ?? 'PDF') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Licencia</p>
                            <p class="text-lg font-semibold">{{ $material->materialDigital->license ?? 'Libre' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Lecturas</p>
                            <p class="text-lg font-semibold">{{ $material->materialDigital->access_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Availability Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900">Disponibilidad</h3>

                @if($material->type === 'digital')
                    {{-- MATERIAL DIGITAL: Siempre disponible para leer --}}
                    <div class="p-4 bg-green-50 border border-green-200 rounded-xl mb-4">
                        <p class="text-green-800 font-semibold flex items-center gap-2">
                            ✅ Disponible para leer
                        </p>
                    </div>

                    {{-- Botón para leer el material digital --}}
                    @if($material->materialDigital && $material->materialDigital->url)
                        <a href="{{ $material->materialDigital->url }}" target="_blank"
                            class="w-full block text-center px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg">
                            📖 Leer Ahora
                        </a>
                        <p class="text-gray-500 text-xs text-center mt-2">Se abrirá en una nueva pestaña</p>
                    @endif
                @else
                    {{-- MATERIAL FÍSICO: Mostrar stock y botón de préstamo --}}
                    @if($material->isAvailable())
                        <div class="p-4 bg-green-50 border border-green-200 rounded-xl mb-4">
                            <p class="text-green-800 font-semibold flex items-center gap-2">
                                ✅ Disponible
                            </p>
                        </div>
                    @else
                        <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-4">
                            <p class="text-red-800 font-semibold flex items-center gap-2">
                                ❌ No Disponible
                            </p>
                        </div>
                    @endif

                    @if($material->materialFisico)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Stock Disponible</p>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                @php
                                    $percentage = $material->materialFisico->stock > 0
                                        ? ($material->materialFisico->available / $material->materialFisico->stock) * 100
                                        : 0;
                                @endphp
                                <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <p class="text-sm mt-2">{{ $material->materialFisico->available }} de
                                {{ $material->materialFisico->stock }} ejemplares
                            </p>
                        </div>
                    @endif

                    {{-- Botón para préstamo (solo materiales físicos) --}}
                    @auth
                        @if($material->isAvailable())
                            @can('approve_loan')
                                {{-- Admin/Trabajador: Crear préstamo asignando a un estudiante --}}
                                <a href="{{ route('loans.create') }}?material_id={{ $material->id }}"
                                    class="w-full block text-center px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-bold hover:from-green-700 hover:to-emerald-700 transition shadow-lg">
                                    📚 Asignar Préstamo
                                </a>
                                <p class="text-gray-500 text-xs text-center mt-2">Selecciona un estudiante para asignarle este libro</p>
                            @else
                                {{-- Estudiante: Solicitar préstamo --}}
                                <a href="{{ route('loan-requests.index') }}"
                                    class="w-full block text-center px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-bold hover:from-green-700 hover:to-emerald-700 transition shadow-lg">
                                    📚 Solicitar Préstamo
                                </a>
                            @endcan
                        @else
                            <button disabled class="w-full px-4 py-3 bg-gray-200 text-gray-500 rounded-xl font-bold cursor-not-allowed">
                                Sin stock disponible
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="w-full block text-center px-4 py-3 bg-gray-600 text-white rounded-xl font-bold hover:bg-gray-700 transition">
                            Inicia sesión para solicitar
                        </a>
                    @endauth
                @endif
            </div>

            <!-- Admin Actions -->
            @can('edit_material')
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-900">⚙️ Administración</h3>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('materials.edit', $material) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition text-center font-medium">
                            ✏️ Editar
                        </a>
                        <form action="{{ route('materials.destroy', $material) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition font-medium"
                                onclick="return confirm('¿Está seguro de eliminar este material?')">
                                🗑️ Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection