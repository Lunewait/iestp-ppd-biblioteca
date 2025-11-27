@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Detalles de Reserva</h2>
                        <p class="text-sm text-gray-600 mt-1">ID: {{ $reservation->id }}</p>
                    </div>
                    <div>
                        @if($reservation->estado === 'pendiente')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock"></i> Pendiente
                            </span>
                        @elseif($reservation->estado === 'completada')
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                <i class="fas fa-check-circle"></i> Completada
                            </span>
                        @else
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                <i class="fas fa-times-circle"></i> Cancelada
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Usuario</label>
                        <p class="text-lg text-gray-800 mt-1">
                            <i class="fas fa-user text-blue-600"></i> {{ $reservation->user->name }}
                        </p>
                        <p class="text-sm text-gray-600">{{ $reservation->user->email }}</p>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Material</label>
                        <p class="text-lg text-gray-800 mt-1">
                            <i class="fas fa-book text-purple-600"></i> {{ $reservation->material->titulo }}
                        </p>
                        <p class="text-sm text-gray-600">{{ $reservation->material->categoria }}</p>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Posición en Cola</label>
                        <p class="text-lg text-gray-800 mt-1 font-bold">
                            <i class="fas fa-list-ol text-orange-600"></i> {{ $reservation->posicion ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Fecha de Reserva</label>
                        <p class="text-gray-800 mt-1">
                            <i class="fas fa-calendar text-blue-600"></i> {{ $reservation->fecha_reserva->format('d/m/Y') }}
                        </p>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Fecha de Creación</label>
                        <p class="text-gray-800 mt-1">
                            <i class="fas fa-clock text-gray-600"></i> {{ $reservation->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    @if($reservation->estado !== 'pendiente')
                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Fecha de {{ $reservation->estado === 'completada' ? 'Completación' : 'Cancelación' }}</label>
                        <p class="text-gray-800 mt-1">
                            <i class="fas fa-{{ $reservation->estado === 'completada' ? 'check' : 'times' }} text-{{ $reservation->estado === 'completada' ? 'green' : 'red' }}-600"></i> {{ $reservation->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                </div>

                <div class="flex gap-3">
                    @if($reservation->estado === 'pendiente')
                        @can('reservations.completar')
                        <form action="{{ route('reservations.complete', $reservation->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                <i class="fas fa-check"></i> Completar Reserva
                            </button>
                        </form>
                        @endcan

                        @can('reservations.cancelar')
                        <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition" onclick="return confirm('¿Cancelar esta reserva?')">
                                <i class="fas fa-times"></i> Cancelar Reserva
                            </button>
                        </form>
                        @endcan

                        @can('reservations.editar')
                        <a href="{{ route('reservations.edit', $reservation->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        @endcan
                    @endif

                    <a href="{{ route('reservations.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>

                    @can('reservations.eliminar')
                    <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition" onclick="return confirm('¿Eliminar esta reserva?')">
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
