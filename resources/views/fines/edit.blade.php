@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Editar Multa</h2>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('fines.update', $fine->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Usuario</label>
                        <select id="user_id" name="user_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" readonly disabled>
                            <option value="{{ $fine->user->id }}">{{ $fine->user->name }}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="monto" class="block text-sm font-medium text-gray-700">Monto (S/.)</label>
                        <input type="number" id="monto" name="monto" value="{{ old('monto', $fine->monto) }}" step="0.01" min="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('monto') border-red-500 @enderror" required>
                        @error('monto')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="razon" class="block text-sm font-medium text-gray-700">Razón</label>
                        <textarea id="razon" name="razon" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('razon') border-red-500 @enderror" required>{{ old('razon', $fine->razon) }}</textarea>
                        @error('razon')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('fines.show', $fine->id) }}" class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
