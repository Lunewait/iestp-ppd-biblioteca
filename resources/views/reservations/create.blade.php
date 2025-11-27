@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Nueva Reserva</h2>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('reservations.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Usuario</label>
                        <select id="user_id" name="user_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('user_id') border-red-500 @enderror" required>
                            <option value="">Seleccionar usuario...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="material_id" class="block text-sm font-medium text-gray-700">Material</label>
                        <select id="material_id" name="material_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('material_id') border-red-500 @enderror" required>
                            <option value="">Seleccionar material...</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>{{ $material->titulo }} ({{ $material->cantidad }} disponibles)</option>
                            @endforeach
                        </select>
                        @error('material_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="fecha_reserva" class="block text-sm font-medium text-gray-700">Fecha de Reserva</label>
                        <input type="date" id="fecha_reserva" name="fecha_reserva" value="{{ old('fecha_reserva', now()->format('Y-m-d')) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('fecha_reserva') border-red-500 @enderror" required>
                        @error('fecha_reserva')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-plus"></i> Crear Reserva
                        </button>
                        <a href="{{ route('reservations.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
