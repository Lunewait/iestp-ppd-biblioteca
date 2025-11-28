<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">✅ Aprobación de Préstamos</h1>
            <p class="text-gray-600 mt-2">Revisa y aprueba las solicitudes de préstamo de los estudiantes</p>
        </div>

        <!-- Filtros -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Filtro de Estado -->
            <div>
                <label for="filterStatus" class="block text-sm font-semibold text-gray-700 mb-2">
                    Estado
                </label>
                <select
                    id="filterStatus"
                    wire:model.live="filterStatus"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                >
                    <option value="pending">⏳ Pendientes</option>
                    <option value="approved">✅ Aprobadas</option>
                    <option value="rejected">❌ Rechazadas</option>
                    <option value="cancelled">🚫 Canceladas</option>
                </select>
            </div>

            <!-- Búsqueda -->
            <div>
                <label for="searchQuery" class="block text-sm font-semibold text-gray-700 mb-2">
                    🔍 Buscar
                </label>
                <input
                    type="text"
                    id="searchQuery"
                    wire:model.live="searchQuery"
                    placeholder="Buscar por estudiante, material..."
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                />
            </div>
        </div>

        <!-- Tabla de Solicitudes -->
        @if ($loans->count() > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border-t-4 border-orange-500">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Estudiante</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Material</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Fecha Solicitud</th>
                                <th class="px-6 py-3 text-center text-sm font-bold text-gray-700">Estado</th>
                                <th class="px-6 py-3 text-center text-sm font-bold text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($loans as $loan)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $loan->usuario->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $loan->usuario->email }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-gray-900">{{ $loan->material->title }}</p>
                                        <p class="text-xs text-gray-500">por {{ $loan->material->author }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $loan->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            @switch($loan->approval_status)
                                                @case('pending')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case('approved')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('rejected')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @case('cancelled')
                                                    bg-gray-100 text-gray-800
                                                    @break
                                            @endswitch
                                        ">
                                            {{ ucfirst($loan->approval_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($loan->approval_status === 'pending')
                                            <div class="flex gap-2 justify-center">
                                                <button
                                                    wire:click="openApprovalModal({{ $loan->id }}, 'approve')"
                                                    class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition"
                                                >
                                                    ✓ Aprobar
                                                </button>
                                                <button
                                                    wire:click="openApprovalModal({{ $loan->id }}, 'reject')"
                                                    class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition"
                                                >
                                                    ✕ Rechazar
                                                </button>
                                            </div>
                                        @else
                                            <button
                                                type="button"
                                                class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition"
                                                @click="$dispatch('open-detail', { loanId: {{ $loan->id }} })"
                                            >
                                                📋 Ver Detalles
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $loans->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <p class="text-gray-600 text-lg">No hay solicitudes 
                    @switch($filterStatus)
                        @case('pending') pendientes @break
                        @case('approved') aprobadas @break
                        @case('rejected') rechazadas @break
                        @case('cancelled') canceladas @break
                    @endswitch
                </p>
            </div>
        @endif
    </div>

    <!-- Modal de Aprobación/Rechazo -->
    @if ($showApprovalModal && $approvingLoanId)
        @php
            $loanToApprove = \App\Models\Prestamo::find($approvingLoanId);
        @endphp

        <div class="fixed inset-0 bg-black bg-opacity-50 z-40"></div>
        <div class="fixed inset-0 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-2xl max-w-md w-full p-6">
                <!-- Header -->
                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">
                        @if ($actionType === 'approve')
                            ✅ Aprobar Préstamo
                        @else
                            ❌ Rechazar Préstamo
                        @endif
                    </h2>
                    <p class="text-gray-600 mt-1">{{ $loanToApprove->material->title }}</p>
                </div>

                <!-- Info del Préstamo -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4 space-y-2">
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Estudiante:</span>
                        <span class="text-gray-900">{{ $loanToApprove->usuario->name }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Material:</span>
                        <span class="text-gray-900">{{ $loanToApprove->material->title }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="font-semibold text-gray-700">Razón:</span>
                        <span class="text-gray-900">{{ $loanToApprove->notas ?: 'N/A' }}</span>
                    </div>
                </div>

                <!-- Formulario -->
                <form>
                    <!-- Comentarios/Razón -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            @if ($actionType === 'approve')
                                Comentario (Opcional)
                            @else
                                Razón de Rechazo *
                            @endif
                        </label>
                        <textarea
                            wire:model="approvalReason"
                            rows="3"
                            placeholder="Escribe un comentario o razón..."
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                        ></textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-3">
                        @if ($actionType === 'approve')
                            <button
                                type="button"
                                wire:click="approveLoan"
                                class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition"
                            >
                                ✓ Aprobar
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="rejectLoan"
                                class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition"
                            >
                                ✕ Rechazar
                            </button>
                        @endif

                        <button
                            type="button"
                            wire:click="closeApprovalModal"
                            class="flex-1 px-4 py-3 bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold rounded-lg transition"
                        >
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
