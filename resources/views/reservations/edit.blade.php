@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Editar Reserva</h2>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reservations.update', $reservation->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Usuario</label>
                        <select id="user_id" name="user_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" readonly disabled>
                            <option value="{{ $reservation->user->id }}">{{ $reservation->user->name }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="material_id" class="block text-sm font-medium text-gray-700">Material</label>
                        <select id="material_id" name="material_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" readonly disabled>
                            <option value="{{ $reservation->material->id }}">{{ $reservation->material->titulo }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="fecha_reserva" class="block text-sm font-medium text-gray-700">Fecha de Reserva</label>
                        <input type="date" id="fecha_reserva" name="fecha_reserva" value="{{ old('fecha_reserva', $reservation->fecha_reserva->format('Y-m-d')) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('fecha_reserva') border-red-500 @enderror" required>
                        @error('fecha_reserva')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="posicion" class="block text-sm font-medium text-gray-700">Posición en Cola</label>
                        <input type="number" id="posicion" name="posicion" value="{{ old('posicion', $reservation->posicion) }}" min="1" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('posicion') border-red-500 @enderror">
                        @error('posicion')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('reservations.show', $reservation->id) }}" class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
