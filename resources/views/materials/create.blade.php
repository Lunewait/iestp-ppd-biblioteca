@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            
            <!-- Header con Gradiente -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                            <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Nuevo Material Bibliográfico
                        </h1>
                        <p class="text-blue-100 mt-1 text-sm">Complete la información para registrar un nuevo libro o recurso digital.</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
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

                <form action="{{ route('materials.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Título -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Título del Material</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">📚</span>
                                </div>
                                <input type="text" name="title" id="title" 
                                    class="pl-10 block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition sm:text-sm py-2.5" 
                                    placeholder="Ej: Clean Code: A Handbook of Agile Software Craftsmanship"
                                    value="{{ old('title') }}" required>
                            </div>
                        </div>

                        <!-- Autor -->
                        <div>
                            <label for="author" class="block text-sm font-semibold text-gray-700 mb-1">Autor</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">✍️</span>
                                </div>
                                <input type="text" name="author" id="author" 
                                    class="pl-10 block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition sm:text-sm py-2.5" 
                                    placeholder="Ej: Robert C. Martin"
                                    value="{{ old('author') }}" required>
                            </div>
                        </div>

                        <!-- Código -->
                        <div>
                            <label for="code" class="block text-sm font-semibold text-gray-700 mb-1">Código de Inventario</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">🔢</span>
                                </div>
                                <input type="text" name="code" id="code" 
                                    class="pl-10 block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition sm:text-sm py-2.5" 
                                    placeholder="Ej: LIB-001-2025"
                                    value="{{ old('code') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de Material -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Material</label>
                        <select name="type" id="type" 
                            class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg transition" 
                            required onchange="toggleFields()">
                            <option value="">Seleccione una opción...</option>
                            <option value="fisico" {{ old('type') === 'fisico' ? 'selected' : '' }}>📘 Físico (Libro impreso)</option>
                            <option value="digital" {{ old('type') === 'digital' ? 'selected' : '' }}>💻 Digital (PDF/E-book)</option>
                            <option value="hibrido" {{ old('type') === 'hibrido' ? 'selected' : '' }}>🔄 Híbrido (Ambos)</option>
                        </select>
                    </div>

                    <!-- Campos Físicos -->
                    <div id="physical_fields" class="hidden transform transition-all duration-300 ease-in-out">
                        <div class="bg-blue-50 rounded-xl p-6 border border-blue-100 space-y-4">
                            <h3 class="font-bold text-blue-900 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Detalles del Material Físico
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="stock" class="block text-sm font-medium text-blue-800 mb-1">Stock Disponible</label>
                                    <input type="number" name="stock" id="stock" 
                                        class="block w-full rounded-lg border-blue-200 focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-2" 
                                        value="{{ old('stock', 1) }}" min="0">
                                </div>
                                <div>
                                    <label for="location" class="block text-sm font-medium text-blue-800 mb-1">Ubicación</label>
                                    <input type="text" name="location" id="location" 
                                        class="block w-full rounded-lg border-blue-200 focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-2" 
                                        placeholder="Ej: Estante A, Fila 3"
                                        value="{{ old('location') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos Digitales -->
                    <div id="digital_fields" class="hidden transform transition-all duration-300 ease-in-out">
                        <div class="bg-green-50 rounded-xl p-6 border border-green-100 space-y-4">
                            <h3 class="font-bold text-green-900 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                Detalles del Material Digital
                            </h3>
                            <div>
                                <label for="url" class="block text-sm font-medium text-green-800 mb-1">URL del Documento</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-green-200 bg-green-100 text-green-600 sm:text-sm">
                                        https://
                                    </span>
                                    <input type="url" name="url" id="url" 
                                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-green-500 focus:border-green-500 sm:text-sm border-green-200" 
                                        placeholder="ejemplo.com/documento.pdf"
                                        value="{{ old('url') }}">
                                </div>
                                <p class="mt-1 text-xs text-green-600">Ingrese el enlace directo al recurso (PDF, Drive, Web).</p>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Descripción / Resumen</label>
                        <div class="mt-1">
                            <textarea name="description" id="description" rows="4" 
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-lg" 
                                placeholder="Breve descripción del contenido del material...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Palabras Clave -->
                    <div>
                        <label for="keywords" class="block text-sm font-semibold text-gray-700 mb-1">Palabras Clave</label>
                        <input type="text" name="keywords" id="keywords" 
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-lg py-2.5 px-3" 
                            placeholder="Ej: programación, java, backend (separadas por comas)"
                            value="{{ old('keywords') }}">
                    </div>

                    <!-- Botones de Acción -->
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('materials.index') }}" 
                           class="bg-white py-2.5 px-5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            Cancelar
                        </a>
                        <button type="submit" 
                            class="inline-flex justify-center py-2.5 px-5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Guardar Material
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFields() {
        const type = document.getElementById('type').value;
        const physicalFields = document.getElementById('physical_fields');
        const digitalFields = document.getElementById('digital_fields');

        if (type === 'fisico') {
            physicalFields.classList.remove('hidden');
            digitalFields.classList.add('hidden');
        } else if (type === 'digital') {
            physicalFields.classList.add('hidden');
            digitalFields.classList.remove('hidden');
        } else if (type === 'hibrido') {
            physicalFields.classList.remove('hidden');
            digitalFields.classList.remove('hidden');
        } else {
            physicalFields.classList.add('hidden');
            digitalFields.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', toggleFields);
</script>
@endsection