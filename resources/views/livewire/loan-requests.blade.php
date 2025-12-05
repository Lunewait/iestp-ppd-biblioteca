<div class="space-y-6">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div
            class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="bg-gradient-to-r from-red-500 to-rose-500 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header con Toggle Moderno --}}
    <div
        class="bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-600 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
        {{-- Elementos decorativos --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
                    @if($showCatalog)
                        <span class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">📚</span>
                        Catálogo de Libros
                    @else
                        <span class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">📋</span>
                        Mis Solicitudes
                    @endif
                </h1>
                <p class="text-purple-100 text-lg">
                    @if($showCatalog)
                        Explora nuestra colección y solicita préstamos
                    @else
                        Gestiona el estado de tus solicitudes
                    @endif
                </p>
            </div>
            <button wire:click="toggleView"
                class="px-6 py-3 bg-white text-purple-600 rounded-2xl font-bold hover:bg-purple-50 transition shadow-lg flex items-center gap-2">
                @if($showCatalog)
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    Ver Mis Solicitudes
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    Ver Catálogo
                @endif
            </button>
        </div>
    </div>

    @if($showCatalog)
        {{-- Indicador de Límite --}}
        @php
            $activeLoanCount = \App\Models\Prestamo::getActiveRequestsCount(auth()->id());
            $maxLoans = config('library.max_active_loans_per_user', 3);
            $remainingSlots = max(0, $maxLoans - $activeLoanCount);
            $percentage = $maxLoans > 0 ? ($activeLoanCount / $maxLoans) * 100 : 0;
            $hasDebt = auth()->user()->blocked_for_loans || auth()->user()->multas()->where('status', 'pendiente')->sum('monto') > 0;
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        {{ $activeLoanCount }}/{{ $maxLoans }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Límite de Préstamos</h3>
                        <p class="text-gray-500 text-sm">
                            @if($hasDebt)
                                <span class="text-red-600 font-medium">⚠️ Tienes deudas pendientes</span>
                            @elseif($remainingSlots > 0)
                                Puedes solicitar <span class="text-green-600 font-bold">{{ $remainingSlots }}</span> libros más
                            @else
                                <span class="text-amber-600 font-medium">Has alcanzado el límite</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Barra de Progreso --}}
                <div class="flex-1 max-w-xs">
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-500 
                                {{ $percentage >= 100 ? 'bg-amber-500' : ($percentage >= 66 ? 'bg-yellow-400' : 'bg-green-500') }}"
                            style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barra de Búsqueda Moderna --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live="search" placeholder="Buscar por título, autor o código..."
                    class="w-full pl-12 pr-4 py-4 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 text-lg">
            </div>
        </div>

        {{-- Grid de Libros --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($materials as $material)
                @php
                    $available = $material->materialFisico->available ?? 0;
                    $total = $material->materialFisico->stock ?? 0;
                    $canRequest = $remainingSlots > 0 && !$hasDebt && $available > 0;
                @endphp

                <div
                    class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-purple-200 transition-all duration-300">
                    {{-- Cabecera con gradiente --}}
                    <div class="h-3 bg-gradient-to-r 
                                {{ $available > 0 ? 'from-green-400 to-emerald-500' : 'from-gray-300 to-gray-400' }}">
                    </div>

                    <div class="p-6">
                        {{-- Título y Autor --}}
                        <div class="mb-4">
                            <h3
                                class="text-xl font-bold text-gray-900 group-hover:text-purple-600 transition line-clamp-2 mb-2">
                                {{ $material->title }}
                            </h3>
                            <p class="text-gray-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ $material->author }}
                            </p>
                        </div>

                        {{-- Info del Material --}}
                        <div class="flex items-center justify-between mb-5 py-3 border-y border-gray-100">
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                                {{ $material->code }}
                            </div>

                            {{-- Disponibilidad --}}
                            @if($available > 0)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                    ✓ {{ $available }} disponible{{ $available > 1 ? 's' : '' }}
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">
                                    ✕ Agotado
                                </span>
                            @endif
                        </div>

                        {{-- Botón de Acción --}}
                        @if($available <= 0)
                            <button disabled
                                class="w-full py-3 px-4 bg-gray-100 text-gray-400 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                    </path>
                                </svg>
                                Sin Stock
                            </button>
                        @elseif($hasDebt)
                            <button disabled
                                class="w-full py-3 px-4 bg-red-50 text-red-400 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                                Paga tu deuda primero
                            </button>
                        @elseif($remainingSlots <= 0)
                            <button disabled
                                class="w-full py-3 px-4 bg-amber-50 text-amber-500 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Límite Alcanzado
                            </button>
                        @else
                            <button wire:click="requestLoan({{ $material->id }})" wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                                class="w-full py-3 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-indigo-700 transition shadow-lg shadow-purple-200 flex items-center justify-center gap-2">
                                <svg wire:loading.remove wire:target="requestLoan({{ $material->id }})" class="w-5 h-5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <svg wire:loading wire:target="requestLoan({{ $material->id }})" class="w-5 h-5 animate-spin"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                <span wire:loading.remove wire:target="requestLoan({{ $material->id }})">Solicitar Préstamo</span>
                                <span wire:loading wire:target="requestLoan({{ $material->id }})">Procesando...</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No se encontraron libros</h3>
                    <p class="text-gray-500">Intenta con otros términos de búsqueda</p>
                </div>
            @endforelse
        </div>

    @else
        {{-- Vista de Solicitudes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center gap-4">
                <label class="text-sm font-semibold text-gray-600">Filtrar por estado:</label>
                <select wire:model.live="statusFilter"
                    class="px-4 py-2 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="all">Todos</option>
                    <option value="approved">📦 Pendiente de recoger</option>
                    <option value="collected">📖 En préstamo</option>
                    <option value="returned">✅ Devueltos</option>
                    <option value="expired">⏰ Expirados</option>
                    <option value="cancelled">❌ Cancelados</option>
                </select>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($reservations as $loan)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="flex items-stretch">
                        {{-- Indicador lateral --}}
                        <div class="w-2 flex-shrink-0
                                    @if($loan->approval_status === 'approved' && $loan->status === 'pendiente_recogida') bg-green-500
                                    @elseif($loan->approval_status === 'collected' && $loan->status === 'activo') bg-blue-500
                                    @elseif($loan->status === 'devuelto') bg-gray-400
                                    @elseif($loan->approval_status === 'expired') bg-amber-500
                                    @else bg-gray-300
                                    @endif">
                        </div>

                        <div class="flex-1 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4">
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
                                        <p class="text-gray-400 text-xs mt-1">Solicitado:
                                            {{ $loan->fecha_prestamo?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2">
                                    {{-- Estado --}}
                                    @if($loan->approval_status === 'approved' && $loan->status === 'pendiente_recogida')
                                        <span class="px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                            📦 Listo para recoger
                                        </span>
                                        @if($loan->fecha_limite_recogida)
                                            @php
                                                $hoursLeft = now()->diffInHours($loan->fecha_limite_recogida, false);
                                            @endphp
                                            <span class="text-xs {{ $hoursLeft <= 0 ? 'text-red-600 font-bold' : 'text-orange-600' }}">
                                                @if($hoursLeft <= 0)
                                                    ⚠️ Tiempo expirado
                                                @else
                                                    ⏰ {{ round($hoursLeft) }} horas restantes
                                                @endif
                                            </span>
                                        @endif
                                    @elseif($loan->approval_status === 'collected' && $loan->status === 'activo')
                                        <span class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                            📖 En préstamo
                                        </span>
                                        @if($loan->fecha_devolucion_esperada)
                                            <span class="text-xs text-gray-500">
                                                Devolver antes del {{ $loan->fecha_devolucion_esperada->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    @elseif($loan->status === 'devuelto')
                                        <span class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">
                                            ✅ Devuelto
                                        </span>
                                    @elseif($loan->approval_status === 'expired')
                                        <span class="px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">
                                            ⏰ No recogido
                                        </span>
                                    @elseif($loan->approval_status === 'cancelled')
                                        <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-full text-xs font-bold">
                                            ❌ Cancelado
                                        </span>
                                    @endif

                                    {{-- Botón cancelar --}}
                                    @if($loan->approval_status === 'approved' && $loan->status === 'pendiente_recogida')
                                        <button wire:click="cancelRequest({{ $loan->id }})" wire:confirm="¿Cancelar esta solicitud?"
                                            class="text-xs text-red-600 hover:text-red-700 font-medium">
                                            Cancelar solicitud
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Mensaje informativo --}}
                            @if($loan->approval_status === 'approved' && $loan->status === 'pendiente_recogida')
                                <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-4">
                                    <p class="text-green-800 text-sm">
                                        <strong>¡Aprobado!</strong> Acércate a la biblioteca con tu documento de identidad para
                                        recoger el libro.
                                    </p>
                                </div>
                            @elseif($loan->approval_status === 'expired')
                                <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-4">
                                    <p class="text-amber-800 text-sm">
                                        Esta solicitud expiró porque no recogiste el libro en las 24 horas establecidas.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Sin solicitudes</h3>
                    <p class="text-gray-500 mb-6">Aún no has realizado ninguna solicitud de préstamo</p>
                    <button wire:click="toggleView"
                        class="px-6 py-3 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition">
                        Explorar Catálogo
                    </button>
                </div>
            @endforelse
        </div>

        @if($reservations->count() > 0)
            <div class="flex justify-center mt-6">
                {{ $reservations->links() }}
            </div>
        @endif
    @endif
</div>