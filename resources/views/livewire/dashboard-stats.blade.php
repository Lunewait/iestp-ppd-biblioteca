<div class="space-y-6">
    {{-- Alerta para estudiantes: Solicitudes APROBADAS pendientes de recoger --}}
    @if($this->hasApprovedLoansToCollect)
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white animate-pulse">
            <div class="flex items-start gap-4">
                <div class="text-4xl">🎉</div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold mb-2">¡Tienes {{ $this->approvedLoansToCollect->count() }} solicitud(es) APROBADA(S)!</h3>
                    <p class="text-green-100 mb-4">
                        Apersónate a la biblioteca para recoger tus materiales. Tienes 24 horas desde la aprobación para recogerlos.
                    </p>
                    <div class="space-y-2">
                        @foreach($this->approvedLoansToCollect as $loan)
                            <div class="bg-white/20 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold">📖 {{ $loan->material->title }}</p>
                                        <p class="text-sm text-green-100">
                                            @if($loan->fecha_limite_recogida)
                                                ⏰ Límite: {{ $loan->fecha_limite_recogida->format('d/m/Y H:i') }}
                                                @if($loan->fecha_limite_recogida->isPast())
                                                    <span class="text-red-200 font-bold">(¡EXPIRADO!)</span>
                                                @else
                                                    ({{ $loan->fecha_limite_recogida->diffForHumans() }})
                                                @endif
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('loans.index') }}" class="inline-block mt-4 px-4 py-2 bg-white text-green-600 font-bold rounded-lg hover:bg-green-50 transition">
                        Ver mis préstamos →
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Alerta para Admin/Trabajador: Préstamos con plazo de recogida expirado --}}
    @can('approve_loan')
        @if($this->expiredCollectionLoans->count() > 0)
            <div class="bg-gradient-to-r from-red-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-start gap-4">
                    <div class="text-4xl">⚠️</div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold mb-2">{{ $this->expiredCollectionLoans->count() }} préstamo(s) con plazo de recogida EXPIRADO</h3>
                        <p class="text-red-100 mb-4">
                            Los siguientes estudiantes no recogieron sus materiales en el plazo de 24 horas. Considera marcarlos como expirados para devolver el stock.
                        </p>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($this->expiredCollectionLoans as $loan)
                                <div class="bg-white/20 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold">📖 {{ $loan->material->title }}</p>
                                            <p class="text-sm text-red-100">
                                                👤 {{ $loan->usuario->name }} | 
                                                Límite: {{ $loan->fecha_limite_recogida?->format('d/m/Y H:i') ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('loan-approvals.index') }}?filterStatus=approved" class="inline-block mt-4 px-4 py-2 bg-white text-red-600 font-bold rounded-lg hover:bg-red-50 transition">
                            Gestionar préstamos →
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Total Materials -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100">Total de Materiales</p>
                    <p class="text-4xl font-bold">{{ $this->totalMaterials }}</p>
                </div>
                <div class="text-5xl opacity-20">📚</div>
            </div>
        </div>

        <!-- Available Materials -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100">Disponibles</p>
                    <p class="text-4xl font-bold">{{ $this->availableMaterials }}</p>
                </div>
                <div class="text-5xl opacity-20">✅</div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100">Préstamos Activos</p>
                    <p class="text-4xl font-bold">{{ $this->activeLoanCount }}</p>
                </div>
                <div class="text-5xl opacity-20">📋</div>
            </div>
        </div>

        <!-- Overdue Loans -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100">Vencidos</p>
                    <p class="text-4xl font-bold">{{ $this->overdueLoanCount }}</p>
                </div>
                <div class="text-5xl opacity-20">⚠️</div>
            </div>
        </div>

        <!-- Pending Fines -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100">Multas Pendientes</p>
                    <p class="text-4xl font-bold">{{ $this->pendingFines }}</p>
                </div>
                <div class="text-5xl opacity-20">💰</div>
            </div>
        </div>

        <!-- Total Pending Amount -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100">Monto Pendiente</p>
                    <p class="text-3xl font-bold">${{ number_format($this->totalPendingFines, 2) }}</p>
                </div>
                <div class="text-5xl opacity-20">💵</div>
            </div>
        </div>
    </div>

    <!-- Recent Loans -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Préstamos Recientes</h2>
        
        @if($this->recentLoans->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Material</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Usuario</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Fecha Préstamo</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Vencimiento</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($this->recentLoans as $loan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $loan->material->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $loan->usuario->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $loan->fecha_prestamo ? $loan->fecha_prestamo->format('d/m/Y') : 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $loan->fecha_devolucion_esperada ? $loan->fecha_devolucion_esperada->format('d/m/Y') : 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($loan->status === 'devuelto')
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Devuelto</span>
                                    @elseif($loan->status === 'vencido')
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Vencido</span>
                                    @else
                                        @php
                                            $daysUntil = $loan->fecha_devolucion_esperada ? $loan->fecha_devolucion_esperada->diffInDays(now(), false) : null;
                                        @endphp
                                        @if($daysUntil !== null && $daysUntil < 0)
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Vencido</span>
                                        @elseif($daysUntil !== null && $daysUntil <= 3)
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Próximo</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Activo</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-gray-500 py-8">No hay préstamos recientes</p>
        @endif
    </div>
</div>
