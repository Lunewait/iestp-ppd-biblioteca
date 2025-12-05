@extends('layouts.app')

@section('title', 'Asignar Préstamo')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl p-6 mb-6 text-white">
            <h1 class="text-2xl font-bold flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-lg">📚</div>
                Asignar Préstamo
            </h1>
            <p class="text-green-100 mt-1">Selecciona un estudiante y material para crear el préstamo</p>
        </div>

        <form action="{{ route('loans.store') }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            @csrf

            <!-- Seleccionar Usuario -->
            <div class="mb-6">
                <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">👤 Estudiante</label>
                <select name="user_id" id="user_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('user_id') border-red-500 @enderror"
                    required>
                    <option value="">Seleccionar estudiante...</option>
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

            <!-- Seleccionar Material -->
            <div class="mb-6">
                <label for="material_id" class="block text-sm font-semibold text-gray-700 mb-2">📖 Material (solo físicos
                    con stock)</label>
                <select name="material_id" id="material_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('material_id') border-red-500 @enderror"
                    required>
                    <option value="">Seleccionar material...</option>
                    @foreach($materials as $material)
                        @if($material->materialFisico && $material->materialFisico->available > 0)
                            <option value="{{ $material->id }}" {{ old('material_id') == $material->id || request('material_id') == $material->id ? 'selected' : '' }}
                                data-stock="{{ $material->materialFisico->available }}">
                                {{ $material->title }} - {{ $material->author }}
                                (Stock: {{ $material->materialFisico->available }}/{{ $material->materialFisico->stock }})
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('material_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror

                <!-- Info del material seleccionado -->
                <div id="material-info" class="mt-3 p-4 bg-blue-50 rounded-xl hidden">
                    <p class="text-blue-800 text-sm font-medium">📦 Stock disponible: <span id="stock-display">-</span></p>
                </div>
            </div>

            <!-- Fecha de Devolución -->
            <div class="mb-6">
                <label for="fecha_devolucion_esperada" class="block text-sm font-semibold text-gray-700 mb-2">📅 Fecha de
                    Devolución Esperada</label>
                <input type="date" name="fecha_devolucion_esperada" id="fecha_devolucion_esperada"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('fecha_devolucion_esperada') border-red-500 @enderror"
                    value="{{ old('fecha_devolucion_esperada', now()->addDays(7)->format('Y-m-d')) }}"
                    min="{{ now()->addDay()->format('Y-m-d') }}" required>
                @error('fecha_devolucion_esperada')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Por defecto: 7 días desde hoy</p>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition font-bold shadow-lg">
                    ✅ Crear Préstamo
                </button>
                <a href="{{ route('materials.index') }}"
                    class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('material_id').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const infoDiv = document.getElementById('material-info');
            const stockDisplay = document.getElementById('stock-display');

            if (selectedOption.value) {
                const stock = selectedOption.dataset.stock;
                stockDisplay.textContent = stock + ' ejemplares';
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        });

        // Trigger on page load if material is pre-selected
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('material_id').dispatchEvent(new Event('change'));
        });
    </script>
@endsection