@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Subir Documento al Repositorio</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('repository.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="titulo" class="block text-gray-700 font-bold mb-2">Título</label>
                <input type="text" name="titulo" id="titulo" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('titulo') }}" required>
                @error('titulo')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Author -->
            <div>
                <label for="autor" class="block text-gray-700 font-bold mb-2">Autor</label>
                <input type="text" name="autor" id="autor" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('autor') }}" required>
                @error('autor')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Type -->
            <div>
                <label for="tipo" class="block text-gray-700 font-bold mb-2">Tipo de Documento</label>
                <select name="tipo" id="tipo" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Seleccionar tipo</option>
                    <option value="tesis" {{ old('tipo') === 'tesis' ? 'selected' : '' }}>Tesis</option>
                    <option value="investigacion" {{ old('tipo') === 'investigacion' ? 'selected' : '' }}>Investigación</option>
                    <option value="articulo" {{ old('tipo') === 'articulo' ? 'selected' : '' }}>Artículo</option>
                    <option value="libro" {{ old('tipo') === 'libro' ? 'selected' : '' }}>Libro</option>
                    <option value="otro" {{ old('tipo') === 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
                @error('tipo')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Description -->
            <div>
                <label for="descripcion" class="block text-gray-700 font-bold mb-2">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('descripcion') }}</textarea>
                @error('descripcion')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- File Upload -->
            <div>
                <label for="archivo" class="block text-gray-700 font-bold mb-2">Archivo del Documento (PDF, DOC, DOCX)</label>
                <input type="file" name="archivo" id="archivo" class="w-full border border-gray-300 rounded px-3 py-2" 
                       accept=".pdf,.doc,.docx" required>
                <small class="text-gray-500">Máximo 10MB. Formatos aceptados: PDF, DOC, DOCX</small>
                @error('archivo')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Keywords -->
            <div>
                <label for="palabras_clave" class="block text-gray-700 font-bold mb-2">Palabras Clave</label>
                <input type="text" name="palabras_clave" id="palabras_clave" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('palabras_clave') }}" placeholder="Separadas por comas">
                @error('palabras_clave')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- License -->
            <div>
                <label for="licencia" class="block text-gray-700 font-bold mb-2">Licencia</label>
                <select name="licencia" id="licencia" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Seleccionar licencia</option>
                    <option value="cc_by" {{ old('licencia') === 'cc_by' ? 'selected' : '' }}>Creative Commons - Atribución</option>
                    <option value="cc_by_sa" {{ old('licencia') === 'cc_by_sa' ? 'selected' : '' }}>Creative Commons - Compartir Igual</option>
                    <option value="cc_by_nc" {{ old('licencia') === 'cc_by_nc' ? 'selected' : '' }}>Creative Commons - No Comercial</option>
                    <option value="all_rights_reserved" {{ old('licencia') === 'all_rights_reserved' ? 'selected' : '' }}>Todos los Derechos Reservados</option>
                </select>
                @error('licencia')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-upload mr-2"></i> Subir Documento
                </button>
                <a href="{{ route('repository.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
