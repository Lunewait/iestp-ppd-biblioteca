@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto py-6">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Editar Material
                </h1>
                <p class="text-blue-100 mt-1">{{ $material->title }}</p>
            </div>

            <div class="p-8">
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg mb-6">
                        <div class="flex">
                            <div class="text-red-400 mr-3">⚠️</div>
                            <div>
                                <h3 class="text-sm font-medium text-red-800">Se encontraron errores:</h3>
                                <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('materials.update', $material) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Título -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">📚 Título</label>
                            <input type="text" name="title" id="title"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                value="{{ old('title', $material->title) }}" required>
                        </div>

                        <!-- Autor -->
                        <div>
                            <label for="author" class="block text-sm font-semibold text-gray-700 mb-2">✍️ Autor</label>
                            <input type="text" name="author" id="author"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                value="{{ old('author', $material->author) }}" required>
                        </div>

                        <!-- Código -->
                        <div>
                            <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">🔢 Código</label>
                            <input type="text" name="code" id="code"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                value="{{ old('code', $material->code) }}" required>
                        </div>
                    </div>

                    <!-- Tipo (Solo lectura) -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Material</label>
                        <div class="flex items-center gap-3">
                            <span
                                class="px-4 py-2 rounded-lg text-sm font-medium
                                {{ $material->type === 'fisico' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ $material->type === 'fisico' ? '📘 Físico' : '💻 Digital' }}
                            </span>
                            <span class="text-gray-500 text-sm">El tipo no puede cambiarse después de la creación</span>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">📝
                            Descripción</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">{{ old('description', $material->description) }}</textarea>
                    </div>

                    <!-- Palabras Clave -->
                    <div>
                        <label for="keywords" class="block text-sm font-semibold text-gray-700 mb-2">🏷️ Palabras
                            Clave</label>
                        <input type="text" name="keywords" id="keywords"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            value="{{ old('keywords', $material->keywords) }}" placeholder="Separadas por comas">
                    </div>

                    <!-- Botones -->
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('materials.show', $material) }}"
                            class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition shadow-lg">
                            💾 Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection