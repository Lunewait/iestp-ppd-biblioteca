@extends('layouts.app')

@php
    // Generar código automático
    $year = date('Y');
    $lastMaterial = \App\Models\Material::orderBy('id', 'desc')->first();
    $nextNumber = $lastMaterial ? $lastMaterial->id + 1 : 1;
    $autoCode = 'LIB-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '-' . $year;
@endphp

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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                                Nuevo Material Bibliográfico
                            </h1>
                            <p class="text-blue-100 mt-1 text-sm">Complete la información para registrar un nuevo libro o
                                recurso digital.</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
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

                    <form action="{{ route('materials.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Título -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">📚 Título del
                                    Material</label>
                                <input type="text" name="title" id="title"
                                    class="block w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition py-2.5 px-4"
                                    placeholder="Ej: Clean Code: A Handbook of Agile Software Craftsmanship"
                                    value="{{ old('title') }}" required>
                            </div>

                            <!-- Autor -->
                            <div>
                                <label for="author" class="block text-sm font-semibold text-gray-700 mb-1">✍️ Autor</label>
                                <input type="text" name="author" id="author"
                                    class="block w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 transition py-2.5 px-4"
                                    placeholder="Ej: Robert C. Martin" value="{{ old('author') }}" required>
                            </div>

                            <!-- Código (Auto-generado) -->
                            <div>
                                <label for="code" class="block text-sm font-semibold text-gray-700 mb-1">🔢 Código de
                                    Inventario</label>
                                <input type="text" name="code" id="code"
                                    class="block w-full rounded-xl border-gray-300 bg-gray-100 focus:ring-blue-500 focus:border-blue-500 transition py-2.5 px-4"
                                    value="{{ old('code', $autoCode) }}" required readonly>
                                <p class="mt-1 text-xs text-gray-500">Generado automáticamente</p>
                            </div>
                        </div>

                        <!-- Tipo de Material -->
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de
                                Material</label>
                            <select name="type" id="type"
                                class="block w-full px-4 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-xl transition"
                                required onchange="toggleFields()">
                                <option value="">Seleccione una opción...</option>
                                <option value="fisico" {{ old('type') === 'fisico' ? 'selected' : '' }}>📘 Físico (Libro
                                    impreso)</option>
                                <option value="digital" {{ old('type') === 'digital' ? 'selected' : '' }}>💻 Digital
                                    (PDF/E-book)</option>
                            </select>
                        </div>

                        <!-- Campos Físicos -->
                        <div id="physical_fields" class="hidden transform transition-all duration-300 ease-in-out">
                            <div class="bg-blue-50 rounded-xl p-6 border border-blue-100 space-y-4">
                                <h3 class="font-bold text-blue-900 flex items-center gap-2">
                                    📚 Detalles del Material Físico
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="isbn" class="block text-sm font-medium text-blue-800 mb-1">ISBN</label>
                                        <input type="text" name="isbn" id="isbn"
                                            class="block w-full rounded-lg border-blue-200 focus:ring-blue-500 focus:border-blue-500 py-2 px-3"
                                            placeholder="Ej: 978-0132350884" value="{{ old('isbn', '978-000-000-0000') }}">
                                    </div>
                                    <div>
                                        <label for="publisher"
                                            class="block text-sm font-medium text-blue-800 mb-1">Editorial</label>
                                        <input type="text" name="publisher" id="publisher"
                                            class="block w-full rounded-lg border-blue-200 focus:ring-blue-500 focus:border-blue-500 py-2 px-3"
                                            placeholder="Ej: Prentice Hall"
                                            value="{{ old('publisher', 'Editorial IESTP') }}">
                                    </div>
                                    <div>
                                        <label for="publication_year"
                                            class="block text-sm font-medium text-blue-800 mb-1">Año de Publicación</label>
                                        <input type="number" name="publication_year" id="publication_year"
                                            class="block w-full rounded-lg border-blue-200 focus:ring-blue-500 focus:border-blue-500 py-2 px-3"
                                            value="{{ old('publication_year', date('Y')) }}" min="1900"
                                            max="{{ date('Y') }}">
                                    </div>
                                    <div>
                                        <label for="stock" class="block text-sm font-medium text-blue-800 mb-1">Stock
                                            Disponible</label>
                                        <input type="number" name="stock" id="stock"
                                            class="block w-full rounded-lg border-blue-200 focus:ring-blue-500 focus:border-blue-500 py-2 px-3"
                                            value="{{ old('stock', 1) }}" min="1">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="location" class="block text-sm font-medium text-blue-800 mb-1">Ubicación
                                            en Biblioteca</label>
                                        <input type="text" name="location" id="location"
                                            class="block w-full rounded-lg border-blue-200 focus:ring-blue-500 focus:border-blue-500 py-2 px-3"
                                            placeholder="Ej: Estante A, Fila 3"
                                            value="{{ old('location', 'Estante A, Fila 1') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos Digitales -->
                        <div id="digital_fields" class="hidden transform transition-all duration-300 ease-in-out">
                            <div class="bg-green-50 rounded-xl p-6 border border-green-100 space-y-4">
                                <h3 class="font-bold text-green-900 flex items-center gap-2">
                                    💻 Detalles del Material Digital
                                </h3>
                                <div>
                                    <label for="url" class="block text-sm font-medium text-green-800 mb-1">URL del
                                        Documento</label>
                                    <input type="url" name="url" id="url"
                                        class="block w-full rounded-lg border-green-200 focus:ring-green-500 focus:border-green-500 py-2 px-3"
                                        placeholder="https://ejemplo.com/documento.pdf" value="{{ old('url') }}">
                                    <p class="mt-1 text-xs text-green-600">Ingrese el enlace directo al recurso (PDF, Drive,
                                        Web).</p>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">📝 Descripción /
                                Resumen</label>
                            <textarea name="description" id="description" rows="4"
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full border-gray-300 rounded-xl px-4 py-2"
                                placeholder="Breve descripción del contenido del material...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Palabras Clave -->
                        <div>
                            <label for="keywords" class="block text-sm font-semibold text-gray-700 mb-1">🏷️ Palabras
                                Clave</label>
                            <input type="text" name="keywords" id="keywords"
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full border-gray-300 rounded-xl py-2.5 px-4"
                                placeholder="Ej: programación, java, backend (separadas por comas)"
                                value="{{ old('keywords') }}">
                        </div>

                        <!-- Botones de Acción -->
                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ route('materials.index') }}"
                                class="bg-white py-2.5 px-5 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center py-2.5 px-5 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                💾 Guardar Material
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
            } else {
                physicalFields.classList.add('hidden');
                digitalFields.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
@endsection