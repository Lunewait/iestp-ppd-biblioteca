@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Reservas</h1>
            @can('create_reservation')
                <a href="{{ route('reservations.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i> Nueva Reserva
                </a>
            @endcan
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <form action="{{ route('reservations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select name="status" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos los estados</option>
                    <option value="activa" {{ request('status') === 'activa' ? 'selected' : '' }}>Activa</option>
                    <option value="completada" {{ request('status') === 'completada' ? 'selected' : '' }}>Completada</option>
                    <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>

                <select name="user_id" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos los usuarios</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                <select name="material_id" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos los materiales</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>
                            {{ $material->title }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-3 text-left">Usuario</th>
                        <th class="border p-3 text-left">Material</th>
                        <th class="border p-3 text-center">Posición</th>
                        <th class="border p-3 text-center">Fecha Esperada</th>
                        <th class="border p-3 text-center">Estado</th>
                        <th class="border p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $res)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-3">{{ $res->usuario->name }}</td>
                            <td class="border p-3">{{ $res->material->title }}</td>
                            <td class="border p-3 text-center">{{ $res->posicion }}</td>
                            <td class="border p-3 text-center">{{ $res->fecha_reserva_esperada->format('M d, Y') }}</td>
                            <td class="border p-3 text-center">
                                <span class="px-3 py-1 rounded-full text-white text-sm font-bold
                                    {{ $res->estado === 'activa' ? 'bg-green-500' : 
                                       ($res->estado === 'completada' ? 'bg-blue-500' : 'bg-red-500') }}">
                                    {{ ucfirst($res->estado) }}
                                </span>
                            </td>
                            <td class="border p-3 text-center">
                                <a href="{{ route('reservations.show', $res) }}" class="text-blue-600 hover:underline">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border p-3 text-center text-gray-600">
                                No hay reservas registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $reservations->links() }}
        </div>
    </div>
</div>
@endsection
