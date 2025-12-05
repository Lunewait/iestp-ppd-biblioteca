<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">📖 Solicitar Préstamo</h1>
            <p class="text-gray-600 mt-2">Selecciona un material disponible para solicitar un préstamo</p>
        </div>

        @if ($showForm && $selectedMaterial)
            <!-- Formulario de Solicitud -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Material Seleccionado -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-600">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Material Seleccionado</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Título</label>
                            <p class="text-lg text-gray-900">{{ $selectedMaterial->title }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700">Autor</label>
                            <p class="text-gray-700">{{ $selectedMaterial->author }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Tipo</label>
                                <p class="text-gray-700">{{ ucfirst($selectedMaterial->type) }}</p>
                            </div>
                        </div>

                        @if ($selectedMaterial->type === 'fisico' || $selectedMaterial->type === 'hibrido')
                            <div class="pt-4 border-t">
                                <label class="text-sm font-semibold text-gray-700">Disponible: 
                                    <span class="text-green-600">{{ $selectedMaterial->materialFisico->available ?? 0 }} copia(s)</span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Formulario -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-600">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Detalles de la Solicitud</h2>
                    
                    <form wire:submit.prevent="submitRequest" class="space-y-4">
                        <!-- Razón/Motivo (Opcional) -->
                        <div>
                            <label for="requestReason" class="block text-sm font-semibold text-gray-700 mb-2">
                                Razón/Motivo (Opcional)
                            </label>
                            <textarea
                                id="requestReason"
                                wire:model="requestReason"
                                rows="4"
                                placeholder="Ej: Para el trabajo de investigación de la materia..."
                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
                        </div>

                        <!-- Info de Fechas -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                            <p class="text-sm font-semibold text-blue-900">📅 Términos del Préstamo:</p>
                            <ul class="text-sm text-blue-800 mt-2 space-y-1">
                                <li>✓ Duración: <strong>7 días</strong> después de recoger el material</li>
                                <li>✓ Plazo para recoger: <strong>24 horas</strong> después de la aprobación</li>
                                <li>✓ Multa por vencimiento: S/. 1.50 por día</li>
                            </ul>
                        </div>

                        <!-- Info Pendiente de Aprobación -->
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                            <p class="text-sm font-semibold text-green-900">✅ Aprobación Automática:</p>
                            <p class="text-sm text-green-800 mt-1">
                                Si <strong>no tienes multas pendientes</strong>, tu solicitud será aprobada automáticamente. 
                                Solo tendrás que ir a recoger el material a la biblioteca dentro de las 24 horas.
                            </p>
                        </div>

                        <!-- Si tiene multas -->
                        @if(auth()->user()->hasUnpaidFines())
                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                                <p class="text-sm font-semibold text-yellow-900">⚠️ Tienes Multas Pendientes:</p>
                                <p class="text-sm text-yellow-800 mt-1">
                                    Tu solicitud requerirá aprobación manual por parte del administrador debido a multas pendientes.
                                </p>
                            </div>
                        @endif

                        <!-- Botones -->
                        <div class="flex gap-3 pt-4">
                            <button
                                type="submit"
                                class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition shadow-lg"
                            >
                                ✓ Confirmar Solicitud
                            </button>
                            <button
                                type="button"
                                wire:click="cancelRequest"
                                class="flex-1 px-4 py-3 bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold rounded-lg transition"
                            >
                                ✕ Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <!-- Lista de Materiales Disponibles -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <!-- Buscador -->
                <div class="mb-6">
                    <label for="searchMaterial" class="block text-sm font-semibold text-gray-700 mb-2">
                        🔍 Buscar Material
                    </label>
                    <input
                        type="text"
                        id="searchMaterial"
                        wire:model.live="searchMaterial"
                        placeholder="Buscar por título o autor..."
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                    />
                </div>

                <!-- Tabla de Materiales -->
                @if ($this->availableMaterials->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b-2 border-gray-300">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Título</th>
                                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Autor</th>
                                    <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Tipo</th>
                                    <th class="px-6 py-3 text-center text-sm font-bold text-gray-700">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($this->availableMaterials as $material)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $material->title }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $material->author }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                                @if ($material->type === 'fisico')
                                                    bg-blue-100 text-blue-800
                                                @elseif($material->type === 'digital')
                                                    bg-purple-100 text-purple-800
                                                @else
                                                    bg-green-100 text-green-800
                                                @endif
                                            ">
                                                {{ ucfirst($material->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button
                                                wire:click="selectMaterial({{ $material->id }})"
                                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition"
                                            >
                                                Solicitar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-600 text-lg">
                            @if ($this->searchMaterial)
                                No se encontraron resultados para "{{ $this->searchMaterial }}"
                            @else
                                No hay materiales disponibles en este momento
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
