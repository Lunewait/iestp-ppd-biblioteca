<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    @if(auth()->user()->hasRole('Estudiante'))
                        Mis Préstamos
                    @else
                        Historial de Préstamos
                    @endif
                </h1>
                <p class="text-gray-500 text-sm">Visualiza el estado de tus préstamos</p>
            </div>
        </div>
        @if(auth()->user()->hasRole('Estudiante'))
            <a href="{{ route('loan-requests.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-medium hover:from-purple-700 hover:to-indigo-700 transition shadow-lg shadow-purple-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Solicitar Libro
            </a>
        @endif
    </div>

    <!-- Filtros Modernos -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">🔍 Buscar</label>
                <input type="text" placeholder="Libro o usuario..." wire:model.live="search"
                    class="w-full px-4 py-2.5 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">📊 Estado</label>
                <select wire:model.live="filterStatus"
                    class="w-full px-4 py-2.5 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="all">Todos</option>
                    <option value="activo">📖 Activos</option>
                    <option value="devuelto">✅ Devueltos</option>
                    <option value="vencido">🔴 Vencidos</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">📅 Ordenar</label>
                <select wire:model.live="sortBy"
                    class="w-full px-4 py-2.5 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="created_at">Más reciente</option>
                    <option value="fecha_devolucion_esperada">Por vencimiento</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Lista de Préstamos -->
    <div class="space-y-4">
        @forelse($loans as $loan)
            @php
                $daysUntilDue = $loan->getDaysUntilDue();
                $isOverdue = $loan->isOverdue();
            @endphp
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="flex items-stretch">
                    <!-- Indicador de Estado (Barra Lateral) -->
                    <div class="w-2 flex-shrink-0
                            @if($loan->status === 'devuelto') bg-gray-400
                            @elseif($isOverdue) bg-red-500
                            @elseif($loan->approval_status === 'approved' && !$loan->fecha_recogida) bg-green-500
                            @elseif($loan->approval_status === 'expired') bg-yellow-500
                            @elseif($loan->approval_status === 'cancelled') bg-gray-300
                            @else bg-blue-500
                            @endif">
                    </div>

                    <div class="flex-1 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <!-- Info del Material -->
                            <div class="flex items-start gap-4 flex-1">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $loan->material->title }}</h3>
                                    <p class="text-gray-500 text-sm">{{ $loan->material->author }}</p>
                                    @if(!auth()->user()->hasRole('Estudiante'))
                                        <p class="text-purple-600 text-sm font-medium mt-1">
                                            👤 {{ $loan->usuario->name ?? 'Usuario' }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Fechas -->
                            <div class="text-right text-sm space-y-1 hidden md:block">
                                <div class="text-gray-500">
                                    📅 Préstamo: <span
                                        class="font-medium text-gray-700">{{ $loan->fecha_prestamo?->format('d/m/Y') ?? $loan->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($loan->fecha_devolucion_esperada)
                                    <div class="{{ $isOverdue ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                        ⏰ Vencimiento: <span
                                            class="font-medium">{{ $loan->fecha_devolucion_esperada->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                                @if($loan->fecha_devolucion_actual)
                                    <div class="text-green-600">
                                        ✅ Devuelto: <span
                                            class="font-medium">{{ $loan->fecha_devolucion_actual->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Estado Badge -->
                            <div class="flex flex-col items-end gap-2">
                                @if($loan->status === 'devuelto')
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">
                                        ✅ Devuelto
                                    </span>
                                @elseif($loan->approval_status === 'approved' && !$loan->fecha_recogida)
                                    <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                        📦 Recoger
                                    </span>
                                    @if($loan->fecha_limite_recogida)
                                        @php
                                            $hoursLeft = now()->diffInHours($loan->fecha_limite_recogida, false);
                                        @endphp
                                        <span class="text-xs {{ $hoursLeft <= 0 ? 'text-red-600' : 'text-orange-600' }}">
                                            @if($hoursLeft <= 0)
                                                ⚠️ ¡Expirado!
                                            @else
                                                ⏰ {{ round($hoursLeft) }}h restantes
                                            @endif
                                        </span>
                                    @endif
                                @elseif($loan->approval_status === 'expired')
                                    <span class="px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                                        ⏰ No recogido
                                    </span>
                                @elseif($loan->approval_status === 'cancelled')
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">
                                        ❌ Cancelado
                                    </span>
                                @elseif($isOverdue)
                                    <span class="px-3 py-1.5 bg-red-100 text-red-700 rounded-full text-xs font-bold">
                                        🔴 Vencido ({{ round(abs($daysUntilDue)) }} días)
                                    </span>
                                @elseif($daysUntilDue !== null && $daysUntilDue <= 3)
                                    <span class="px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                                        ⚠️ Vence en {{ round($daysUntilDue) }} días
                                    </span>
                                @else
                                    <span class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                        📖 En préstamo
                                    </span>
                                    @if($daysUntilDue !== null)
                                        <span class="text-xs text-gray-500">{{ round($daysUntilDue) }} días restantes</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Sin préstamos</h3>
                <p class="text-gray-500 mb-6">Aún no tienes préstamos registrados</p>
                @if(auth()->user()->hasRole('Estudiante'))
                    <a href="{{ route('loan-requests.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Solicitar mi primer libro
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($loans->hasPages())
        <div class="flex justify-center">
            {{ $loans->links() }}
        </div>
    @endif
</div>