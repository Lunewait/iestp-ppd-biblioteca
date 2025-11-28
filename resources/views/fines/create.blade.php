@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                <!-- Header con Gradiente de Advertencia -->
                <div class="bg-gradient-to-r from-red-600 to-orange-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                                <svg class="w-8 h-8 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                Registrar Nueva Multa
                            </h1>
                            <p class="text-red-100 mt-1 text-sm">Complete los detalles para aplicar una sanción a un
                                estudiante.</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
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

                    <form action="{{ route('fines.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Selección de Usuario -->
                        <div>
                            <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-1">Estudiante</label>
                            <div class="relative">
                                <select name="user_id" id="user_id"
                                    class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-lg transition shadow-sm"
                                    required>
                                    <option value="">Seleccionar estudiante...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->institutional_email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Monto -->
                            <div>
                                <label for="monto" class="block text-sm font-semibold text-gray-700 mb-1">Monto de la
                                    Multa</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-bold">S/.</span>
                                    </div>
                                    <input type="number" name="monto" id="monto"
                                        class="pl-12 block w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 transition sm:text-sm py-2.5 font-bold text-gray-900"
                                        placeholder="0.00" step="0.01" min="0.01" value="{{ old('monto') }}" required>
                                </div>
                            </div>

                            <!-- Tipo de Incidente -->
                            <div>
                                <label for="tipo_incidente" class="block text-sm font-semibold text-gray-700 mb-1">Tipo de
                                    Incidente</label>
                                <select id="tipo_incidente"
                                    class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-lg transition shadow-sm"
                                    onchange="updateReason(this.value)">
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="Retraso en devolución">⏱️ Retraso en devolución</option>
                                    <option value="Daño leve en material">⚠️ Daño leve en material</option>
                                    <option value="Daño severo en material">🚫 Daño severo en material</option>
                                    <option value="Pérdida de material">❓ Pérdida de material</option>
                                    <option value="otro">📝 Otro motivo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Razón -->
                        <div>
                            <label for="razon" class="block text-sm font-semibold text-gray-700 mb-1">Detalle /
                                Razón</label>
                            <div class="mt-1">
                                <input type="text" name="razon" id="razon"
                                    class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-lg py-3 px-4"
                                    placeholder="Especifique el motivo de la multa..." value="{{ old('razon') }}" required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Este detalle será visible para el estudiante.</p>
                        </div>

                        <script>
                            function updateReason(value) {
                                const razonInput = document.getElementById('razon');
                                if (value && value !== 'otro') {
                                    razonInput.value = value;
                                } else if (value === 'otro') {
                                    razonInput.value = '';
                                    razonInput.placeholder = 'Especifique el motivo manualmente...';
                                    razonInput.focus();
                                }
                            }
                        </script>

                        <!-- Botones -->
                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ route('fines.index') }}"
                                class="bg-white py-2.5 px-5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center py-2.5 px-5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-500 transition transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                Registrar Multa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection