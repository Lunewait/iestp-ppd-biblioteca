@extends('layouts.app')

@section('title', 'Nuevo Préstamo')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Registrar Nuevo Préstamo</h1>

        <form action="{{ route('loans.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf

            <div class="mb-6">
                <label for="user_id" class="block text-sm font-semibold mb-2">Usuario</label>
                <select name="user_id" id="user_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('user_id') border-red-500 @enderror" required>
                    <option value="">Seleccionar usuario...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="material_id" class="block text-sm font-semibold mb-2">Material</label>
                <select name="material_id" id="material_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('material_id') border-red-500 @enderror" required>
                    <option value="">Seleccionar material...</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}" {{ old('material_id') == $material->id || request('material_id') == $material->id ? 'selected' : '' }}>
                            {{ $material->title }} - {{ $material->author }}
                        </option>
                    @endforeach
                </select>
                @error('material_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="fecha_devolucion_esperada" class="block text-sm font-semibold mb-2">Fecha de Devolución Esperada</label>
                <input type="date" name="fecha_devolucion_esperada" id="fecha_devolucion_esperada"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 @error('fecha_devolucion_esperada') border-red-500 @enderror"
                    value="{{ old('fecha_devolucion_esperada') }}" required>
                @error('fecha_devolucion_esperada')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                    <i class="fas fa-save"></i> Registrar Préstamo
                </button>
                <a href="{{ route('loans.index') }}" class="flex-1 px-6 py-3 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition font-semibold text-center">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
