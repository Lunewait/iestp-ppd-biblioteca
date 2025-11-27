@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">📥 Importar Usuarios desde Excel/CSV</h2>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                        ← Volver
                    </a>
                </div>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('import_errors'))
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                        <p class="font-bold mb-2">⚠️ Errores durante la importación:</p>
                        <ul class="list-disc list-inside text-sm">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Instructions -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <h3 class="font-bold text-blue-800 mb-2">📋 Instrucciones:</h3>
                    <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                        <li>Descarga la plantilla de ejemplo haciendo clic en el botón de abajo</li>
                        <li>Completa el archivo con los datos de los usuarios</li>
                        <li>El archivo debe tener las siguientes columnas: <strong>Nombre, Email, Email Institucional, Contraseña, Rol</strong></li>
                        <li>Los roles válidos son: <strong>Admin, Jefe_Area, Trabajador, Estudiante</strong></li>
                        <li>Sube el archivo completado (formatos aceptados: .xlsx, .xls, .csv)</li>
                    </ol>
                </div>

                <!-- Download Template Button -->
                <div class="mb-6">
                    <a href="{{ route('users.import.template') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Descargar Plantilla CSV
                    </a>
                </div>

                <!-- Upload Form -->
                <form action="{{ route('users.import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        
                        <label for="file" class="cursor-pointer">
                            <span class="mt-2 block text-sm font-medium text-gray-700">
                                Selecciona un archivo Excel o CSV
                            </span>
                            <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required
                                   class="mt-2 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100
                                          cursor-pointer">
                        </label>
                        
                        <p class="mt-2 text-xs text-gray-500">
                            Formatos aceptados: XLSX, XLS, CSV (máximo 2MB)
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-semibold">
                            📤 Importar Usuarios
                        </button>
                        <a href="{{ route('users.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                            Cancelar
                        </a>
                    </div>
                </form>

                <!-- Format Example -->
                <div class="mt-8 bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-bold text-gray-800 mb-3">📄 Ejemplo de formato:</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-300">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-4 py-2 border">Nombre</th>
                                    <th class="px-4 py-2 border">Email</th>
                                    <th class="px-4 py-2 border">Email Institucional</th>
                                    <th class="px-4 py-2 border">Contraseña</th>
                                    <th class="px-4 py-2 border">Rol</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-white">
                                    <td class="px-4 py-2 border">Juan Pérez</td>
                                    <td class="px-4 py-2 border">juan.perez@example.com</td>
                                    <td class="px-4 py-2 border">juan.perez@iestp.edu.pe</td>
                                    <td class="px-4 py-2 border">password123</td>
                                    <td class="px-4 py-2 border">Estudiante</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-4 py-2 border">María García</td>
                                    <td class="px-4 py-2 border">maria.garcia@example.com</td>
                                    <td class="px-4 py-2 border">maria.garcia@iestp.edu.pe</td>
                                    <td class="px-4 py-2 border">securepass456</td>
                                    <td class="px-4 py-2 border">Trabajador</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
