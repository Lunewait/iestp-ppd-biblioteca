<div class="space-y-6">
    <!-- Header & Title -->
    <div class="flex items-center gap-3 mb-6">
        <div class="p-2 bg-amber-100 rounded-lg">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                </path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">
            @if(auth()->user()->hasRole('Estudiante'))
                Mis Préstamos
            @else
                Gestión de Préstamos Activos
            @endif
        </h1>
    </div>

    <!-- Enhanced Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Búsqueda y Filtros
            </h2>
            <div class="text-sm text-gray-500">
                Total: <span class="font-bold text-purple-600">{{ $loans->total() }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Search -->
            <div class="md:col-span-6">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Buscar</label>
                <div class="relative">
                    <input type="text" placeholder="Material, usuario..." wire:model.live="search"
                        class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition text-sm">
                </div>
            </div>

            <!-- Filter Status -->
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Estado</label>
                <select wire:model.live="filterStatus"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition text-sm cursor-pointer">
                    <option value="all">Todos</option>
                    <option value="activo">Activos</option>
                    <option value="pending">Pendientes</option>
                    <option value="devuelto">Devueltos</option>
                    <option value="vencido">Vencidos</option>
                </select>
            </div>

            <!-- Sort -->
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ordenar</label>
                <select wire:model.live="sortBy"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition text-sm cursor-pointer">
                    <option value="created_at">Más Reciente</option>
                    <option value="fecha_devolucion_esperada">Vencimiento</option>
                    <option value="user_id">Usuario</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Loans Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-purple-500">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Material</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Préstamo</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Vencimiento</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-purple-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="h-8 w-8 rounded bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                            </path>
                                        </svg>
                                    </div>
                                    <a href="{{ route('materials.show', $loan->material) }}"
                                        class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ Str::limit($loan->material->title, 40) }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 mr-2">
                                        {{ substr($loan->usuario->name ?? 'U', 0, 1) }}
                                    </div>
                                    <span
                                        class="text-sm text-gray-700">{{ $loan->usuario->name ?? 'Usuario Desconocido' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $loan->fecha_prestamo?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $loan->fecha_devolucion_esperada?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($loan->status === 'devuelto')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        Devuelto
                                    </span>
                                @elseif($loan->status === 'pending')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                        Pendiente
                                    </span>
                                @elseif($loan->status === 'rejected')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        Rechazado
                                    </span>
                                @else
                                    @php
                                        $daysUntilDue = $loan->getDaysUntilDue();
                                    @endphp
                                    @if($daysUntilDue < 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                            Vencido ({{ number_format(abs($daysUntilDue), 0) }} días)
                                        </span>
                                    @elseif($daysUntilDue <= 3)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            Próximo a vencer
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            Activo
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                @if($loan->status === 'activo' && !auth()->user()->hasRole('Estudiante'))
                                    <button wire:click="returnLoan({{ $loan->id }})" 
                                            onclick="confirm('¿Confirmar devolución del material?') || event.stopImmediatePropagation()"
                                            class="text-xs bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded border border-green-200 transition">
                                        Devolver
                                    </button>
                                @endif
                                <a href="{{ route('loans.show', $loan) }}"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-900 hover:underline">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500 text-sm">No se encontraron préstamos</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-6">
        {{ $loans->links() }}
    </div>
</div>