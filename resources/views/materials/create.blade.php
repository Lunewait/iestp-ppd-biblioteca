@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Add New Material</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('materials.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
                <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('title') }}" required>
                @error('title')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Author -->
            <div>
                <label for="author" class="block text-gray-700 font-bold mb-2">Author</label>
                <input type="text" name="author" id="author" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('author') }}" required>
                @error('author')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Code -->
            <div>
                <label for="code" class="block text-gray-700 font-bold mb-2">Code</label>
                <input type="text" name="code" id="code" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('code') }}" required>
                @error('code')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-gray-700 font-bold mb-2">Type</label>
                <select name="type" id="type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Select Type</option>
                    <option value="fisico" {{ old('type') === 'fisico' ? 'selected' : '' }}>Physical</option>
                    <option value="digital" {{ old('type') === 'digital' ? 'selected' : '' }}>Digital</option>
                    <option value="hibrido" {{ old('type') === 'hibrido' ? 'selected' : '' }}>Hybrid</option>
                </select>
                @error('type')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('description') }}</textarea>
                @error('description')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Keywords -->
            <div>
                <label for="keywords" class="block text-gray-700 font-bold mb-2">Keywords</label>
                <input type="text" name="keywords" id="keywords" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('keywords') }}" placeholder="Comma separated">
                @error('keywords')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-save mr-2"></i> Save Material
                </button>
                <a href="{{ route('materials.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
