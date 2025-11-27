@extends('layouts.app')

@section('title', $material->title)

@section('content')
    <div class="mb-6">
        <a href="{{ route('materials.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left"></i> Volver al Catálogo
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Material Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">{{ $material->title }}</h1>
                        <p class="text-gray-600 text-lg">
                            <i class="fas fa-user"></i> {{ $material->author }}
                        </p>
                    </div>
                    <span class="px-4 py-2 text-lg font-semibold rounded
                        {{ $material->type === 'fisico' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $material->type === 'digital' ? 'bg-purple-100 text-purple-800' : '' }}
                        {{ $material->type === 'hibrido' ? 'bg-green-100 text-green-800' : '' }}">
                        {{ ucfirst($material->type) }}
                    </span>
                </div>

                <p class="text-gray-600 mb-4">{{ $material->description }}</p>

                <div class="border-t pt-4">
                    <p class="mb-2"><strong>Código:</strong> {{ $material->code }}</p>
                    @if($material->keywords)
                        <p class="mb-2"><strong>Palabras clave:</strong> {{ $material->keywords }}</p>
                    @endif
                </div>
            </div>

            <!-- Physical Material Info -->
            @if($material->materialFisico)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Información Física</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">ISBN</p>
                            <p class="text-lg font-semibold">{{ $material->materialFisico->isbn ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Editorial</p>
                            <p class="text-lg font-semibold">{{ $material->materialFisico->publisher ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Año de Publicación</p>
                            <p class="text-lg font-semibold">{{ $material->materialFisico->publication_year ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Ubicación</p>
                            <p class="text-lg font-semibold">{{ $material->materialFisico->location ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Digital Material Info -->
            @if($material->materialDigital)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Información Digital</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Tipo de Archivo</p>
                            <p class="text-lg font-semibold">{{ strtoupper($material->materialDigital->file_type ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Descargable</p>
                            <p class="text-lg font-semibold">
                                @if($material->materialDigital->downloadable)
                                    <span class="text-green-600"><i class="fas fa-check"></i> Sí</span>
                                @else
                                    <span class="text-red-600"><i class="fas fa-times"></i> No</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Licencia</p>
                            <p class="text-lg font-semibold">{{ $material->materialDigital->license ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Descargas</p>
                            <p class="text-lg font-semibold">{{ $material->materialDigital->access_count }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Availability Card -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">Disponibilidad</h3>
                @if($material->isAvailable())
                    <div class="p-4 bg-green-50 border border-green-200 rounded mb-4">
                        <p class="text-green-800 font-semibold">
                            <i class="fas fa-check-circle"></i> Disponible
                        </p>
                    </div>
                @else
                    <div class="p-4 bg-red-50 border border-red-200 rounded mb-4">
                        <p class="text-red-800 font-semibold">
                            <i class="fas fa-times-circle"></i> No Disponible
                        </p>
                    </div>
                @endif

                @if($material->materialFisico)
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Stock Disponible</p>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full" style="width: {{ ($material->materialFisico->available / $material->materialFisico->stock) * 100 }}%"></div>
                        </div>
                        <p class="text-sm mt-2">{{ $material->materialFisico->available }} de {{ $material->materialFisico->stock }}</p>
                    </div>
                @endif

                @can('create_loan')
                    @if($material->isAvailable())
                        <a href="{{ route('loans.create') }}?material_id={{ $material->id }}" class="w-full block text-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                            <i class="fas fa-handshake"></i> Solicitar Préstamo
                        </a>
                    @endif
                @elseif(auth()->check())
                    <a href="{{ route('repository.create') }}" class="w-full block text-center px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                        <i class="fas fa-plus"></i> Subir a Repositorio
                    </a>
                @endif
            </div>

            <!-- Admin Actions -->
            @can('edit_material')
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold mb-4">Acciones</h3>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('materials.edit', $material) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-center">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('materials.destroy', $material) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition" onclick="return confirm('¿Está seguro?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
