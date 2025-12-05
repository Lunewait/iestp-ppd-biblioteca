@extends('layouts.app')

@section('title', 'Reportes y Estadísticas')

@section('content')
    <div class="space-y-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Reportes y Estadísticas</h1>
                    <p class="text-gray-500 text-sm">Panel de análisis de la biblioteca</p>
                </div>
            </div>

            {{-- Filtro de Fechas --}}
            <form action="{{ route('reports.index') }}" method="GET" class="flex items-end gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Desde</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                        class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Hasta</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                        class="px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium">
                    Filtrar
                </button>
            </form>
        </div>

        {{-- Estadísticas Principales --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Total Materiales --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Materiales</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_materials']) }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            📘 {{ $stats['physical_materials'] }} físicos · 💻 {{ $stats['digital_materials'] }} digitales
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">📚</span>
                    </div>
                </div>
            </div>

            {{-- Total Usuarios --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Usuarios</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            👨‍🎓 {{ $stats['students'] }} estudiantes
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">👥</span>
                    </div>
                </div>
            </div>

            {{-- Préstamos Activos --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Préstamos Activos</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['active_loans']) }}</p>
                        <p class="text-xs {{ $stats['overdue_loans'] > 0 ? 'text-red-500' : 'text-gray-400' }} mt-1">
                            ⚠️ {{ $stats['overdue_loans'] }} vencidos
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">📖</span>
                    </div>
                </div>
            </div>

            {{-- Multas Pendientes --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Multas Pendientes</p>
                        <p class="text-3xl font-bold text-red-600 mt-1">S/. {{ number_format($stats['pending_fines'], 2) }}
                        </p>
                        <p class="text-xs text-green-500 mt-1">
                            ✅ S/. {{ number_format($stats['paid_fines'], 2) }} cobradas
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">💰</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Segunda fila de stats --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-5 text-white">
                <p class="text-blue-100 text-sm font-medium">Total Préstamos</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($stats['total_loans']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl shadow-lg p-5 text-white">
                <p class="text-amber-100 text-sm font-medium">Pendientes Recogida</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($stats['pending_pickups']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl shadow-lg p-5 text-white">
                <p class="text-green-100 text-sm font-medium">Documentos Repo</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($stats['repository_documents']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl shadow-lg p-5 text-white">
                <p class="text-purple-100 text-sm font-medium">Docs Pendientes</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($stats['pending_documents']) }}</p>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-rose-500 rounded-2xl shadow-lg p-5 text-white">
                <p class="text-red-100 text-sm font-medium">Usuarios Bloqueados</p>
                <p class="text-3xl font-bold mt-1">{{ number_format($blockedUsers) }}</p>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Préstamos por Mes --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Préstamos por Mes</h3>
                <div class="space-y-3">
                    @php $maxLoans = max(array_values($monthsData)) ?: 1; @endphp
                    @foreach($monthsData as $month => $count)
                        <div class="flex items-center gap-3">
                            <span
                                class="text-sm text-gray-500 w-20">{{ Carbon\Carbon::parse($month . '-01')->translatedFormat('M Y') }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-full rounded-full flex items-center justify-end pr-2 transition-all duration-500"
                                    style="width: {{ ($count / $maxLoans) * 100 }}%">
                                    @if($count > 0)
                                        <span class="text-xs text-white font-bold">{{ $count }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-700 w-10 text-right">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Multas por Mes --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">💰 Multas Generadas por Mes</h3>
                <div class="space-y-3">
                    @php $maxFines = max(array_values($finesData)) ?: 1; @endphp
                    @foreach($finesData as $month => $amount)
                        <div class="flex items-center gap-3">
                            <span
                                class="text-sm text-gray-500 w-20">{{ Carbon\Carbon::parse($month . '-01')->translatedFormat('M Y') }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-500 to-red-500 h-full rounded-full flex items-center justify-end pr-2 transition-all duration-500"
                                    style="width: {{ $maxFines > 0 ? ($amount / $maxFines) * 100 : 0 }}%">
                                    @if($amount > 0)
                                        <span class="text-xs text-white font-bold">S/.{{ number_format($amount, 0) }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-700 w-16 text-right">S/.
                                {{ number_format($amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Estado de Préstamos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">📈 Distribución de Estados de Préstamos</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-xl">
                    <p class="text-3xl font-bold text-blue-600">{{ $loanStatus['activos'] }}</p>
                    <p class="text-sm text-blue-800 font-medium mt-1">📖 Activos</p>
                </div>
                <div class="text-center p-4 bg-amber-50 rounded-xl">
                    <p class="text-3xl font-bold text-amber-600">{{ $loanStatus['pendiente_recogida'] }}</p>
                    <p class="text-sm text-amber-800 font-medium mt-1">📦 Pendientes</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <p class="text-3xl font-bold text-green-600">{{ $loanStatus['devueltos'] }}</p>
                    <p class="text-sm text-green-800 font-medium mt-1">✅ Devueltos</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-xl">
                    <p class="text-3xl font-bold text-red-600">{{ $loanStatus['vencidos'] }}</p>
                    <p class="text-sm text-red-800 font-medium mt-1">⚠️ Vencidos</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <p class="text-3xl font-bold text-gray-600">{{ $loanStatus['cancelados'] }}</p>
                    <p class="text-sm text-gray-800 font-medium mt-1">❌ Cancelados</p>
                </div>
            </div>
        </div>

        {{-- Rankings --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top 10 Materiales más Prestados --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">🏆 Top 10 Materiales Más Prestados</h3>
                <div class="space-y-3">
                    @forelse($topMaterials as $index => $material)
                        <div
                            class="flex items-center gap-3 p-3 {{ $index < 3 ? 'bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200' : 'bg-gray-50' }} rounded-xl">
                            <div
                                class="w-8 h-8 flex items-center justify-center rounded-full {{ $index == 0 ? 'bg-yellow-400 text-white' : ($index == 1 ? 'bg-gray-300' : ($index == 2 ? 'bg-amber-600 text-white' : 'bg-gray-200')) }} font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $material->title }}</p>
                                <p class="text-xs text-gray-500">{{ $material->author }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-indigo-600">{{ $material->loan_count }}</span>
                                <p class="text-xs text-gray-400">préstamos</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No hay datos de préstamos</p>
                    @endforelse
                </div>
            </div>

            {{-- Top 10 Usuarios con más Préstamos --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">👥 Top 10 Estudiantes Más Activos</h3>
                <div class="space-y-3">
                    @forelse($topUsers as $index => $user)
                        <div
                            class="flex items-center gap-3 p-3 {{ $index < 3 ? 'bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200' : 'bg-gray-50' }} rounded-xl">
                            <div
                                class="w-8 h-8 flex items-center justify-center rounded-full {{ $index == 0 ? 'bg-green-500 text-white' : ($index == 1 ? 'bg-green-300' : ($index == 2 ? 'bg-green-600 text-white' : 'bg-gray-200')) }} font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div
                                class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-green-600">{{ $user->loan_count }}</span>
                                <p class="text-xs text-gray-400">préstamos</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No hay datos de usuarios</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Resumen del Período --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <h3 class="text-lg font-bold mb-4">📅 Resumen del Período: {{ $startDate->format('d/m/Y') }} -
                {{ $endDate->format('d/m/Y') }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <p class="text-4xl font-bold">{{ number_format($loansInPeriod) }}</p>
                    <p class="text-indigo-200 text-sm mt-1">Préstamos Realizados</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold">{{ number_format($returnsInPeriod) }}</p>
                    <p class="text-indigo-200 text-sm mt-1">Devoluciones</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold">
                        {{ $loansInPeriod > 0 ? number_format(($returnsInPeriod / $loansInPeriod) * 100, 1) : 0 }}%</p>
                    <p class="text-indigo-200 text-sm mt-1">Tasa de Devolución</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-bold">{{ $endDate->diffInDays($startDate) }}</p>
                    <p class="text-indigo-200 text-sm mt-1">Días Analizados</p>
                </div>
            </div>
        </div>
    </div>
@endsection