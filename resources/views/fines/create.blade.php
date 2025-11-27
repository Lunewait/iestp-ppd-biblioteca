@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Crear Nueva Multa</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('fines.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Usuario -->
            <div>
                <label for="user_id" class="block text-gray-700 font-bold mb-2">Usuario</label>
                <select name="user_id" id="user_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Seleccionar usuario</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->institutional_email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Monto -->
            <div>
                <label for="monto" class="block text-gray-700 font-bold mb-2">Monto (S/.)</label>
                <input type="number" name="monto" id="monto" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('monto') }}" step="0.01" min="0.01" required>
                @error('monto')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Razón -->
            <div>
                <label for="razon" class="block text-gray-700 font-bold mb-2">Razón</label>
                <input type="text" name="razon" id="razon" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('razon') }}" placeholder="Motivo de la multa" required>
                @error('razon')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-save mr-2"></i> Guardar Multa
                </button>
                <a href="{{ route('fines.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
