<div class="space-y-6">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header with Toggle --}}
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">
                    @if($showCatalog)
                        Solicitar Préstamo
                    @else
                        Mis Solicitudes
                    @endif
                </h1>
                <p class="text-purple-100">
                    @if($showCatalog)
                        Busca y solicita libros disponibles
                    @else
                        Gestiona tus solicitudes y revisa su estado
                    @endif
                </p>
            </div>
            <button wire:click="toggleView"
                class="px-6 py-3 bg-white text-purple-600 rounded-lg font-semibold hover:bg-purple-50 transition">
                @if($showCatalog)
                    <i class="fas fa-list mr-2"></i> Ver Mis Solicitudes
                @else
                    <i class="fas fa-plus-circle mr-2"></i> Solicitar Libro
                @endif
            </button>
        </div>
    </div>

    @if($showCatalog)
        {{-- Catalog View --}}
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <input type="text" wire:model.live="search" placeholder="Buscar por título o autor..."
                class="w-full border border-gray-300 rounded px-4 py-3">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($materials as $material)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $material->title }}</h3>
                        <p class="text-gray-600 mb-4">
                            <i class="fas fa-user mr-2"></i>{{ $material->author }}
                        </p>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-barcode mr-1"></i>{{ $material->code }}
                            </span>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                {{ $material->materialFisico->available }} disponibles
                            </span>
                        </div>
                        <button wire:click="requestLoan({{ $material->id }})"
                            class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                            <i class="fas fa-paper-plane mr-2"></i>Solicitar
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-600">No se encontraron libros disponibles</p>
                </div>
            @endforelse
        </div>
    @else
        {{-- Requests View --}}
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex gap-4">
                <select wire:model.live="statusFilter" class="border border-gray-300 rounded px-4 py-2">
                    <option value="all">Todos los Estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="aprobada">Aprobada</option>
                    <option value="completada">Completada</option>
                    <option value="cancelada">Cancelada</option>
                    <option value="expirada">Expirada</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            @forelse($reservations as $reservation)
                <div class="border-b border-gray-200 p-6 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-bold text-gray-800">{{ $reservation->material->title }}</h3>
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                            {{ $reservation->status === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $reservation->status === 'aprobada' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $reservation->status === 'completada' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $reservation->status === 'cancelada' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $reservation->status === 'expirada' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </div>

                            <p class="text-gray-600 mb-3">
                                <i class="fas fa-user mr-2"></i>{{ $reservation->material->author }}
                            </p>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Fecha de Solicitud:</span>
                                    <span class="font-semibold">{{ $reservation->fecha_reserva->format('d/m/Y H:i') }}</span>
                                </div>

                                @if($reservation->status === 'aprobada' && $reservation->fecha_expiracion)
                                    <div>
                                        <span class="text-gray-500">Expira:</span>
                                        <span
                                            class="font-semibold {{ $reservation->isExpired() ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $reservation->fecha_expiracion->format('d/m/Y H:i') }}
                                            @if(!$reservation->isExpired())
                                                ({{ $reservation->fecha_expiracion->diffForHumans() }})
                                            @else
                                                (Expirado)
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Status Messages --}}
                            @if($reservation->status === 'pendiente')
                                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded p-3">
                                    <p class="text-yellow-800 text-sm">
                                        <i class="fas fa-clock mr-2"></i>
                                        Tu solicitud está en espera de aprobación por el personal de la biblioteca.
                                    </p>
                                </div>
                            @elseif($reservation->status === 'aprobada')
                                <div class="mt-4 bg-green-50 border border-green-200 rounded p-3">
                                    <p class="text-green-800 text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        ¡Solicitud Aprobada! Tienes hasta el
                                        {{ $reservation->fecha_expiracion->format('d/m/Y H:i') }} para recoger el libro en la
                                        biblioteca.
                                    </p>
                                    <p class="text-green-700 text-xs mt-1">
                                        Por favor, acércate con tu documento de identidad. Una vez recogido, tendrás 7 días para
                                        devolverlo.
                                    </p>
                                </div>
                            @elseif($reservation->status === 'expirada')
                                <div class="mt-4 bg-red-50 border border-red-200 rounded p-3">
                                    <p class="text-red-800 text-sm">
                                        <i class="fas fa-times-circle mr-2"></i>
                                        Esta solicitud expiró porque no recogiste el libro en el plazo de 48 horas.
                                    </p>
                                </div>
                            @elseif($reservation->status === 'completada')
                                <div class="mt-4 bg-blue-50 border border-blue-200 rounded p-3">
                                    <p class="text-blue-800 text-sm">
                                        <i class="fas fa-book mr-2"></i>
                                        Préstamo completado. El libro fue entregado y tienes 7 días para devolverlo.
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="ml-4">
                            @if($reservation->status === 'pendiente')
                                <button wire:click="cancelRequest({{ $reservation->id }})"
                                    wire:confirm="¿Estás seguro de cancelar esta solicitud?"
                                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No tienes solicitudes</h3>
                    <p class="text-gray-600 mb-4">Solicita un libro del catálogo para empezar.</p>
                    <button wire:click="toggleView"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-plus-circle mr-2"></i>Solicitar Libro
                    </button>
                </div>
            @endforelse
        </div>

        @if($reservations->count() > 0)
            <div class="mt-4">
                {{ $reservations->links() }}
            </div>
        @endif
    @endif
</div>