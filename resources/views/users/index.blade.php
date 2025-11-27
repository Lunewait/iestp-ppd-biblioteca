@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Usuarios</h1>
            @can('create_user')
                <div class="flex gap-2">
                    <a href="{{ route('users.import.form') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-file-upload mr-2"></i> Importar Excel
                    </a>
                    <a href="{{ route('users.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-plus mr-2"></i> Nuevo Usuario
                    </a>
                </div>
            @endcan
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="search" placeholder="Buscar por nombre o email..." 
                       value="{{ request('search') }}" class="border border-gray-300 rounded px-3 py-2">
                
                <select name="role" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-search mr-2"></i> Buscar
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-3 text-left">Nombre</th>
                        <th class="border p-3 text-left">Email</th>
                        <th class="border p-3 text-left">Email Institucional</th>
                        <th class="border p-3 text-left">Rol</th>
                        <th class="border p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-3">{{ $user->name }}</td>
                            <td class="border p-3">{{ $user->email }}</td>
                            <td class="border p-3">{{ $user->institutional_email }}</td>
                            <td class="border p-3">
                                @foreach($user->roles as $role)
                                    <span class="px-3 py-1 rounded-full text-white text-sm font-bold bg-blue-500 inline-block mr-1">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="border p-3 text-center">
                                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:underline mr-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit_user')
                                    <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:underline mr-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('delete_user')
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" 
                                              onsubmit="return confirm('¿Estás seguro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border p-3 text-center text-gray-600">
                                No hay usuarios registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
