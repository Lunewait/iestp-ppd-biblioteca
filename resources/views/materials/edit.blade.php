@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Edit Material</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('materials.update', $material) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Title -->
            <div>
                <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
                <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('title', $material->title) }}" required>
                @error('title')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Author -->
            <div>
                <label for="author" class="block text-gray-700 font-bold mb-2">Author</label>
                <input type="text" name="author" id="author" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('author', $material->author) }}" required>
                @error('author')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Code -->
            <div>
                <label for="code" class="block text-gray-700 font-bold mb-2">Code</label>
                <input type="text" name="code" id="code" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('code', $material->code) }}" required>
                @error('code')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-gray-700 font-bold mb-2">Type</label>
                <select name="type" id="type" class="w-full border border-gray-300 rounded px-3 py-2" required disabled>
                    <option value="fisico" {{ $material->type === 'fisico' ? 'selected' : '' }}>Physical</option>
                    <option value="digital" {{ $material->type === 'digital' ? 'selected' : '' }}>Digital</option>
                    <option value="hibrido" {{ $material->type === 'hibrido' ? 'selected' : '' }}>Hybrid</option>
                </select>
                <small class="text-gray-500">Type cannot be changed after creation</small>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('description', $material->description) }}</textarea>
                @error('description')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Keywords -->
            <div>
                <label for="keywords" class="block text-gray-700 font-bold mb-2">Keywords</label>
                <input type="text" name="keywords" id="keywords" class="w-full border border-gray-300 rounded px-3 py-2" 
                       value="{{ old('keywords', $material->keywords) }}" placeholder="Comma separated">
                @error('keywords')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-save mr-2"></i> Update Material
                </button>
                <a href="{{ route('materials.show', $material) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
