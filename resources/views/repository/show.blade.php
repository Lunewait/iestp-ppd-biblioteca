@extends('layouts.app')

@section('content')
    @if(auth()->user()->hasRole('Estudiante'))
        {{-- Simplified PDF viewer for students --}}
        <div class="max-w-7xl mx-auto py-6">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold mb-2">{{ $documento->titulo }}</h1>
                            <p class="text-blue-100 flex items-center gap-2">
                                <i class="fas fa-user"></i>
                                {{ $documento->autor }}
                            </p>
                        </div>
                        <a href="{{ route('repository.download', $documento) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white text-blue-700 rounded-lg font-semibold hover:bg-blue-50 transition">
                            <i class="fas fa-download"></i>
                            Descargar PDF
                        </a>
                    </div>
                </div>

                {{-- PDF Viewer --}}
                <div class="p-4 bg-gray-50">
                    <div class="bg-white rounded-lg shadow-inner" style="height: calc(100vh - 250px);">
                        <iframe 
                            src="{{ route('repository.download', $documento) }}" 
                            class="w-full h-full rounded-lg"
                            frameborder="0">
                        </iframe>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                    <a href="{{ route('repository.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Repositorio
                    </a>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-download mr-1"></i>
                        {{ $documento->descargas ?? 0 }} descargas
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Detailed view for admin/workers/jefe_area --}}
        <div class="max-w-4xl mx-auto py-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $documento->titulo }}</h1>
                        <p class="text-gray-600">Por {{ $documento->autor }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-white text-sm font-bold
                        {{ $documento->estado === 'publicado' ? 'bg-green-500' :
            ($documento->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-500') }}">
                        {{ ucfirst($documento->estado) }}
                    </span>
                </div>

                {{-- Document Information --}}
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Detalles del Documento</h2>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-gray-600 font-bold">ID:</dt>
                                <dd class="text-gray-800">{{ $documento->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600 font-bold">Tipo:</dt>
                                <dd class="text-gray-800">{{ ucfirst($documento->tipo) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600 font-bold">Licencia:</dt>
                                <dd class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $documento->licencia)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600 font-bold">Enviado:</dt>
                                <dd class="text-gray-800">{{ $documento->created_at->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600 font-bold">Descargas:</dt>
                                <dd class="text-gray-800">{{ $documento->descargas ?? 0 }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Información del Remitente</h2>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-gray-600 font-bold">Nombre:</dt>
                                <dd class="text-gray-800">{{ $documento->usuario->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600 font-bold">Email:</dt>
                                <dd class="text-gray-800">{{ $documento->usuario->institutional_email }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-600 font-bold">Rol:</dt>
                                <dd class="text-gray-800">{{ $documento->usuario->roles->pluck('name')->join(', ') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-2">Descripción</h2>
                    <p class="text-gray-700">{{ $documento->descripcion }}</p>
                </div>

                {{-- Approval History --}}
                @if($documento->aprobaciones->count() > 0)
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Historial de Aprobaciones</h2>
                        <div class="space-y-4">
                            @foreach($documento->aprobaciones as $approval)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-gray-800 font-bold">{{ $approval->jefeArea->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $approval->jefeArea->roles->pluck('name')->join(', ') }}</p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-white text-sm font-bold
                                            {{ $approval->decision === 'aprobado' ? 'bg-green-500' : 'bg-red-500' }}">
                                            {{ ucfirst($approval->decision) }}
                                        </span>
                                    </div>
                                    @if($approval->comentarios)
                                        <p class="text-gray-700 text-sm mt-2">{{ $approval->comentarios }}</p>
                                    @endif>
                                    <p class="text-xs text-gray-500 mt-2">{{ $approval->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-3">
                    <a href="{{ route('repository.download', $documento) }}" 
                       class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-download mr-2"></i>Descargar
                    </a>

                    @can('approve_document')
                        @if($documento->estado === 'pendiente' || $documento->estado === 'aprobado')
                            <a href="{{ route('repository.approve', $documento) }}" 
                               class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                                <i class="fas fa-check-circle mr-2"></i>Gestionar Aprobación
                            </a>
                        @endif
                    @endcan

                    @role('Admin')
                        <form action="{{ route('repository.destroy', $documento) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                                    onclick="return confirm('¿Eliminar este documento?')">
                                <i class="fas fa-trash mr-2"></i>Eliminar
                            </button>
                        </form>
                    @endrole

                    <a href="{{ route('repository.index') }}" 
                       class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection