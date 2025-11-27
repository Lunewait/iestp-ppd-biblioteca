@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-start mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Approve Document</h1>
            <span class="px-3 py-1 rounded-full text-yellow-800 bg-yellow-100 text-sm font-bold">
                Pending Approval
            </span>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Document Summary -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Document to Review</h2>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-gray-600 font-bold">Title:</dt>
                    <dd class="text-gray-800">{{ $documento->titulo }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-bold">Author:</dt>
                    <dd class="text-gray-800">{{ $documento->autor }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-bold">Type:</dt>
                    <dd class="text-gray-800">{{ ucfirst($documento->tipo) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-bold">Submitted By:</dt>
                    <dd class="text-gray-800">{{ $documento->usuario->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-bold">Submitted Date:</dt>
                    <dd class="text-gray-800">{{ $documento->created_at->format('M d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-bold">License:</dt>
                    <dd class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $documento->licencia)) }}</dd>
                </div>
            </dl>
        </div>

        <!-- Description -->
        @if ($documento->descripcion)
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-3">Description</h2>
                <p class="text-gray-800 whitespace-pre-wrap">{{ $documento->descripcion }}</p>
            </div>
        @endif

        <!-- Current Approvals -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Current Approvals</h2>
            <div class="space-y-3">
                @forelse ($documento->aprobaciones as $approval)
                    <div class="flex items-center justify-between p-3 border-b">
                        <div>
                            <p class="text-gray-800 font-bold">{{ $approval->usuario->name }}</p>
                            <p class="text-gray-600 text-sm">{{ $approval->usuario->roles->pluck('name')->join(', ') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-white text-sm font-bold
                                {{ $approval->estado === 'aprobado' ? 'bg-green-500' : 'bg-red-500' }}">
                                {{ ucfirst($approval->estado) }}
                            </span>
                            <p class="text-gray-600 text-sm mt-1">{{ $approval->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 italic">No approvals yet</p>
                @endforelse
            </div>
        </div>

        <!-- Approval Form -->
        <form action="{{ route('repository.process-approval', $documento) }}" method="POST" class="space-y-6">
            @csrf

            <div class="border-t pt-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Your Approval Decision</h2>

                <!-- Decision -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-3">Decision</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="estado" value="aprobado" class="mr-3" required>
                            <span class="text-green-600 font-bold">✓ Approve</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="estado" value="rechazado" class="mr-3" required>
                            <span class="text-red-600 font-bold">✗ Reject</span>
                        </label>
                    </div>
                    @error('estado')<span class="text-red-500">{{ $message }}</span>@enderror
                </div>

                <!-- Comments -->
                <div>
                    <label for="comentarios" class="block text-gray-700 font-bold mb-2">Comments (Optional)</label>
                    <textarea name="comentarios" id="comentarios" rows="4" 
                              class="w-full border border-gray-300 rounded px-3 py-2"
                              placeholder="Any feedback or reasons for your decision...">{{ old('comentarios') }}</textarea>
                    @error('comentarios')<span class="text-red-500">{{ $message }}</span>@enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-check mr-2"></i> Submit Approval
                </button>
                <a href="{{ route('repository.show', $documento) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
