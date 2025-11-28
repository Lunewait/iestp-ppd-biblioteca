@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Multas</h1>
                @can('create_fine')
                    <a href="{{ route('fines.create') }}"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-plus mr-2"></i> Nueva Multa
                    </a>
                @endcan
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search & Filter Section - Only for Admin/Workers --}}
            @unless(auth()->user()->hasRole('Estudiante'))
                <!-- Filters -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form action="{{ route('fines.index') }}" method="GET" class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <input type="text" name="search" placeholder="Buscar por usuario o motivo..."
                                value="{{ request('search') }}" class="border border-gray-300 rounded px-3 py-2">

                            <!-- Status Filter -->
                            <select name="status" class="border border-gray-300 rounded px-3 py-2">
                                <option value="">Todos los estados</option>
                                <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente
                                </option>
                                <option value="pagada" {{ request('status') === 'pagada' ? 'selected' : '' }}>Pagada</option>
                                <option value="condonada" {{ request('status') === 'condonada' ? 'selected' : '' }}>Condonada
                                </option>
                            </select>

                            <!-- User Filter -->
                            <select name="user_id" class="border border-gray-300 rounded px-3 py-2">
                                <option value="">Todos los usuarios</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-search mr-2"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
            @endunless

            {{-- Payment Information --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
                    <div>
                        <p class="font-semibold text-blue-900 mb-1">Información Importante</p>
                        <p class="text-blue-800 text-sm">
                            Las multas se pagan de manera <strong>presencial</strong> en la biblioteca del instituto.
                            Por favor, acércate con tu documento de identidad para realizar el pago.
                        </p>
                    </div>
                </div>
            </div>

            @unless(auth()->user()->hasRole('Estudiante'))
                @if($fines->count() > 0)
                    {{-- Total Pending - Only for Admin/Workers --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <p class="text-lg">
                            <strong>Total Multas Pendientes:</strong>
                            <span class="text-2xl text-red-600 font-bold">S/. {{ number_format($totalPending, 2) }}</span>
                        </p>
                    </div>
                @endif
            @endunless

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                @if(auth()->user()->hasRole('Estudiante'))
                                    Material
                                @else
                                    Usuario
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Motivo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($fines as $fine)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(auth()->user()->hasRole('Estudiante'))
                                                    <span class="text-sm font-medium text-gray-900">
                                                        {{ $fine->prestamo ? $fine->prestamo->material->title : 'N/A' }}
                                                    </span>
                                                @else
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <a href="{{ route('users.show', $fine->usuario) }}"
                                                            class="text-blue-600 hover:underline">
                                                            {{ $fine->usuario->name }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="border p-3">{{ $fine->razon }}</td>
                                            <td class="border p-3 text-right font-bold">S/. {{ number_format($fine->monto, 2) }}</td>
                                            <td class="border p-3 text-center">
                                                <span class="px-3 py-1 rounded-full text-white text-sm font-bold
                                                                                                                    {{ $fine->status === 'pendiente' ? 'bg-yellow-500' :
                            ($fine->status === 'pagada' ? 'bg-green-500' : 'bg-gray-500') }}">
                                                    {{ ucfirst($fine->status) }}
                                                </span>
                                            </td>
                                            <td class="border p-3 text-center">{{ $fine->created_at->format('M d, Y') }}</td>
                                            <td class="border p-3 text-center">
                                                <a href="{{ route('fines.show', $fine) }}" class="text-blue-600 hover:underline mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('manage_fines')
                                                    <a href="{{ route('fines.edit', $fine) }}" class="text-yellow-600 hover:underline mr-2">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="border p-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-check-circle text-green-600 text-4xl"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800 mb-2">¡Excelente!</h3>
                                        <p class="text-gray-600">No tienes multas pendientes.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $fines->links() }}
            </div>
        </div>
    </div>
@endsection