@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Perfil del Usuario</h2>
                        <p class="text-sm text-gray-600 mt-1">ID: {{ $user->id }}</p>
                    </div>
                    <div>
                        @if($user->roles->count() > 0)
                            @foreach($user->roles as $role)
                                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 mr-2">
                                    <i class="fas fa-shield-alt"></i> {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        @else
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                <i class="fas fa-user"></i> Sin Rol
                            </span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Nombre</label>
                        <p class="text-lg text-gray-800 mt-1">
                            <i class="fas fa-user-circle text-blue-600"></i> {{ $user->name }}
                        </p>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Email</label>
                        <p class="text-lg text-gray-800 mt-1">
                            <i class="fas fa-envelope text-blue-600"></i> {{ $user->email }}
                        </p>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Email Institucional</label>
                        <p class="text-gray-800 mt-1">
                            <i class="fas fa-school text-purple-600"></i> {{ $user->institutional_email ?? 'No asignado' }}
                        </p>
                    </div>

                    <div class="border-b border-gray-200 pb-4">
                        <label class="text-sm font-semibold text-gray-600">Fecha de Registro</label>
                        <p class="text-gray-800 mt-1">
                            <i class="fas fa-calendar text-green-600"></i> {{ $user->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-800 mb-4">Estadísticas</h3>
                <div class="grid grid-cols-4 gap-4 mb-8">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-blue-600">{{ $user->prestamos()->count() }}</p>
                        <p class="text-sm text-gray-600 mt-1">Préstamos</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-red-600">{{ $user->multas()->count() }}</p>
                        <p class="text-sm text-gray-600 mt-1">Multas</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-yellow-600">{{ $user->reservas()->count() }}</p>
                        <p class="text-sm text-gray-600 mt-1">Reservas</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-purple-600">S/. {{ number_format($user->multas()->where('status', 'pendiente')->sum('monto'), 2) }}</p>
                        <p class="text-sm text-gray-600 mt-1">Multas Pendientes</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    @can('edit_user')
                    <a href="{{ route('users.edit', $user->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </a>
                    @endcan

                    <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>

                    @can('delete_user')
                        @if(auth()->user()->id !== $user->id)
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition" onclick="return confirm('¿Eliminar este usuario?')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
