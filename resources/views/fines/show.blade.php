@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Detalles de Multa</h2>
                            <p class="text-sm text-gray-600 mt-1">ID: {{ $fine->id }}</p>
                        </div>
                        <div>
                            @if($fine->status === 'pendiente')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-circle"></i> Pendiente
                                </span>
                            @elseif($fine->status === 'pagada')
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle"></i> Pagada
                                </span>
                            @else
                                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-ban"></i> Condonada
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="border-b border-gray-200 pb-4">
                            <label class="text-sm font-semibold text-gray-600">Usuario</label>
                            <p class="text-lg text-gray-800 mt-1">
                                <i class="fas fa-user text-blue-600"></i> {{ $fine->usuario->name }}
                            </p>
                            <p class="text-sm text-gray-600">{{ $fine->usuario->institutional_email }}</p>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <label class="text-sm font-semibold text-gray-600">Monto</label>
                            <p class="text-lg text-gray-800 mt-1 font-bold">
                                <i class="fas fa-dollar-sign text-green-600"></i> S/. {{ number_format($fine->monto, 2) }}
                            </p>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <label class="text-sm font-semibold text-gray-600">Razón</label>
                            <p class="text-gray-800 mt-1">{{ $fine->razon }}</p>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <label class="text-sm font-semibold text-gray-600">Fecha de Creación</label>
                            <p class="text-gray-800 mt-1">
                                <i class="fas fa-calendar text-blue-600"></i> {{ $fine->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        @if($fine->status === 'pagada')
                            <div class="border-b border-gray-200 pb-4">
                                <label class="text-sm font-semibold text-gray-600">Fecha de Pago</label>
                                <p class="text-gray-800 mt-1">
                                    <i class="fas fa-check text-green-600"></i> {{ $fine->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        @endif

                        @if($fine->status === 'condonada')
                            <div class="border-b border-gray-200 pb-4">
                                <label class="text-sm font-semibold text-gray-600">Fecha de Condonación</label>
                                <p class="text-gray-800 mt-1">
                                    <i class="fas fa-ban text-blue-600"></i> {{ $fine->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-3">
                        @if($fine->status === 'pendiente')
                            @can('manage_fines')
                                <form action="{{ route('fines.mark-as-paid', $fine->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                        <i class="fas fa-check"></i> Marcar como Pagada
                                    </button>
                                </form>
                            @endcan

                            @can('forgive_fine')
                                <form action="{{ route('fines.forgive', $fine->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                                        onclick="return confirm('¿Condonar esta multa?')">
                                        <i class="fas fa-ban"></i> Condonar Multa
                                    </button>
                                </form>
                            @endcan

                            @can('manage_fines')
                                <a href="{{ route('fines.edit', $fine->id) }}"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            @endcan
                        @endif

                        <a href="{{ route('fines.index') }}"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>

                        @can('manage_fines')
                            <form action="{{ route('fines.destroy', $fine->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                                    onclick="return confirm('¿Eliminar esta multa?')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection