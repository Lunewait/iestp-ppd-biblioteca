@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $documento->titulo }}</h1>
                <p class="text-gray-600">By {{ $documento->autor }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-white text-sm font-bold
                {{ $documento->estado === 'publicado' ? 'bg-green-500' : 
                   ($documento->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-500') }}">
                {{ ucfirst($documento->estado) }}
            </span>
        </div>

        <!-- Document Information -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-4">Document Details</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-gray-600 font-bold">ID:</dt>
                        <dd class="text-gray-800">{{ $documento->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Type:</dt>
                        <dd class="text-gray-800">{{ ucfirst($documento->tipo) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">License:</dt>
                        <dd class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $documento->licencia)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Submitted:</dt>
                        <dd class="text-gray-800">{{ $documento->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Downloads:</dt>
                        <dd class="text-gray-800">{{ $documento->descargas ?? 0 }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-4">Submitter Information</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-gray-600 font-bold">Name:</dt>
                        <dd class="text-gray-800">{{ $documento->usuario->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Email:</dt>
                        <dd class="text-gray-800">{{ $documento->usuario->institutional_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Role:</dt>
                        <dd class="text-gray-800">{{ $documento->usuario->roles->pluck('name')->join(', ') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Description -->
        @if ($documento->descripcion)
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-3">Description</h2>
                <p class="text-gray-800 whitespace-pre-wrap">{{ $documento->descripcion }}</p>
            </div>
        @endif

        <!-- Keywords -->
        @if ($documento->palabras_clave)
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-3">Keywords</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach (explode(',', $documento->palabras_clave) as $keyword)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            {{ trim($keyword) }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Approvals -->
        @if ($documento->aprobaciones->count() > 0)
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Approvals</h2>
                <div class="space-y-3">
                    @foreach ($documento->aprobaciones as $approval)
                        <div class="flex items-center justify-between p-3 border-b">
                            <div>
                                <p class="text-gray-800 font-bold">{{ $approval->usuario->name }}</p>
                                <p class="text-gray-600 text-sm">{{ $approval->usuario->roles->pluck('name')->join(', ') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-white text-sm font-bold
                                    {{ $approval->estado === 'aprobado' ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ ucfirst($approval->estado) }}
                                </span>
                                <p class="text-gray-600 text-sm mt-1">{{ $approval->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex gap-4">
            @if ($documento->estado === 'publicado')
                <a href="{{ route('repository.download', $documento) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-download mr-2"></i> Download Document
                </a>
            @endif
            
            @if (auth()->user()->hasPermissionTo('approve_document') && $documento->estado === 'pendiente')
                <a href="{{ route('repository.approve', $documento) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-check mr-2"></i> Review & Approve
                </a>
            @endif
            
            <a href="{{ route('repository.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Repository
            </a>
        </div>
    </div>
</div>
@endsection
