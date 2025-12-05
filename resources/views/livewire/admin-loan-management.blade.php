<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestión de Préstamos</h1>
                <p class="text-gray-500 text-sm">Entrega y recibe libros de los estudiantes</p>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div
            class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">✓</div>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('warning'))
        <div
            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">⚠️</div>
            <span class="font-medium">{{ session('warning') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div
            class="bg-gradient-to-r from-red-500 to-rose-500 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">✕</div>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Filtros --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">🔍 Buscar</label>
                <input type="text" wire:model.live="search" placeholder="Estudiante o libro..."
                    class="w-full px-4 py-2.5 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">📊 Filtrar</label>
                <select wire:model.live="statusFilter"
                    class="w-full px-4 py-2.5 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500">
                    <option value="pending_pickup">📦 Pendientes de entrega</option>
                    <option value="active">📖 Préstamos activos</option>
                    <option value="overdue">🔴 Vencidos</option>
                    <option value="all">📋 Todos</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Lista de Préstamos --}}
    <div class="space-y-4">
        @forelse($loans as $loan)
            @php
                $isOverdue = $loan->fecha_devolucion_esperada && $loan->fecha_devolucion_esperada->isPast() && $loan->status === 'activo';
                $isPendingPickup = $loan->approval_status === 'approved' && $loan->status === 'pendiente_recogida';
                $isActive = $loan->approval_status === 'collected' && $loan->status === 'activo';
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="flex items-stretch">
                    {{-- Indicador lateral --}}
                    <div class="w-2 flex-shrink-0
                            @if($isPendingPickup) bg-green-500
                            @elseif($isOverdue) bg-red-500
                            @elseif($isActive) bg-blue-500
                            @elseif($loan->status === 'devuelto') bg-gray-400
                            @else bg-gray-300
                            @endif">
                    </div>

                    <div class="flex-1 p-5">
                        <div class="flex items-start justify-between gap-4">
                            {{-- Información del Estudiante y Material --}}
                            <div class="flex items-start gap-4">
                                {{-- Avatar --}}
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                                    {{ substr($loan->usuario->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $loan->usuario->name ?? 'Usuario' }}</h3>
                                    <p class="text-gray-500 text-sm">{{ $loan->usuario->email ?? '' }}</p>
                                    <p class="text-purple-600 font-medium mt-1">📚
                                        {{ Str::limit($loan->material->title, 40) }}</p>
                                    <p class="text-gray-400 text-xs">{{ $loan->material->code }}</p>
                                </div>
                            </div>

                            {{-- Fechas y Estado --}}
                            <div class="text-right space-y-2">
                                @if($isPendingPickup)
                                    <span
                                        class="px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-bold inline-block">
                                        📦 Pendiente de entrega
                                    </span>
                                    @if($loan->fecha_limite_recogida)
                                                            @php
                                                                $hoursLeft = now()->diffInHours($loan->fecha_limite_recogida, false);
                                                            @endphp
                                         <div
                                                                class="text-sm {{ $hoursLeft <= 0 ? 'text-red-600 font-bold' : ($hoursLeft <= 6 ? 'text-orange-600' : 'text-gray-500') }}">
                                                                @if($hoursLeft <= 0)
                                                                    ⚠️ ¡Tiempo expirado!
                                                                @else
                                                                    ⏰ {{ round($hoursLeft) }} horas restantes
                                                                @endif
                                                            </div>
                                    @endif
                                @elseif($isOverdue)
                                    <span
                                        class="px-3 py-1.5 bg-red-100 text-red-700 rounded-full text-xs font-bold inline-block">
                                        🔴 Vencido
                                    </span>
                                    @php
                                        $daysLate = round(now()->diffInDays($loan->fecha_devolucion_esperada));
                                    @endphp
                                    <div class="text-sm text-red-600 font-medium">
                                        {{ $daysLate }} días de retraso
                                    </div>
                                @elseif($isActive)
                                    <span
                                        class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold inline-block">
                                        📖 En préstamo
                                    </span>
                                    @if($loan->fecha_devolucion_esperada)
                                        <div class="text-sm text-gray-500">
                                            Vence: {{ $loan->fecha_devolucion_esperada->format('d/m/Y') }}
                                        </div>
                                    @endif
                                @elseif($loan->status === 'devuelto')
                                    <span
                                        class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-xs font-bold inline-block">
                                        ✅ Devuelto
                                    </span>
                                @elseif($loan->approval_status === 'expired')
                                    <span
                                        class="px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold inline-block">
                                        ⏰ No recogido
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-full text-xs font-bold inline-block">
                                        {{ ucfirst($loan->status ?? 'Desconocido') }}
                                    </span>
                                @endif
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="flex flex-col gap-2">
                                @if($isPendingPickup)
                                    <button wire:click="deliver({{ $loan->id }})"
                                        wire:confirm="¿Entregar libro al estudiante? Se iniciará el préstamo de 7 días."
                                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold hover:from-blue-700 hover:to-indigo-700 transition shadow-lg text-sm flex items-center gap-2">
                                        📦 Entregar
                                    </button>
                                    <button wire:click="cancelLoan({{ $loan->id }})"
                                        wire:confirm="¿Cancelar este préstamo? El libro volverá al stock."
                                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition text-sm flex items-center gap-2">
                                        ✕ Cancelar
                                    </button>
                                @endif

                                @if($isActive)
                                    <button wire:click="receive({{ $loan->id }})"
                                        wire:confirm="¿Confirmar devolución del libro? Se verificará si hay retraso."
                                        class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-bold hover:from-green-700 hover:to-emerald-700 transition shadow-lg text-sm flex items-center gap-2">
                                        ✅ Recibir
                                    </button>
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
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Sin préstamos pendientes</h3>
                <p class="text-gray-500">No hay préstamos que gestionar en este momento</p>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($loans->hasPages())
        <div class="flex justify-center">
            {{ $loans->links() }}
        </div>
    @endif
</div>