@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Register New Loan</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('loans.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- User -->
            <div>
                <label for="user_id" class="block text-gray-700 font-bold mb-2">Student</label>
                <select name="user_id" id="user_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Select a student</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->institutional_email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Material -->
            <div>
                <label for="material_id" class="block text-gray-700 font-bold mb-2">Material</label>
                <select name="material_id" id="material_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Select a material</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>
                            {{ $material->title }} ({{ $material->code }}) - {{ ucfirst($material->type) }}
                        </option>
                    @endforeach
                </select>
                @error('material_id')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Due Date -->
            <div>
                <label for="fecha_devolucion_esperada" class="block text-gray-700 font-bold mb-2">Due Date</label>
                <input type="date" name="fecha_devolucion_esperada" id="fecha_devolucion_esperada" 
                       class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('fecha_devolucion_esperada') }}" required>
                <small class="text-gray-500">Must be after today</small>
                @error('fecha_devolucion_esperada')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Notes (Optional) -->
            <div>
                <label for="notas" class="block text-gray-700 font-bold mb-2">Notes (Optional)</label>
                <textarea name="notas" id="notas" rows="3" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('notas') }}</textarea>
                @error('notas')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i> Create Loan
                </button>
                <a href="{{ route('loans.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
