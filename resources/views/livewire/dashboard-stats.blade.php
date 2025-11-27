<div class="space-y-6">
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
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $loan->fecha_prestamo->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $loan->fecha_devolucion_esperada->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($loan->status === 'devuelto')
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Devuelto</span>
                                    @elseif($loan->status === 'vencido')
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Vencido</span>
                                    @else
                                        @php
                                            $daysUntil = $loan->fecha_devolucion_esperada->diffInDays(now(), false);
                                        @endphp
                                        @if($daysUntil < 0)
                                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Vencido</span>
                                        @elseif($daysUntil <= 3)
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
