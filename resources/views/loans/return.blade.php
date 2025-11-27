@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Return Loan</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Loan Summary -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Loan Details</h2>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt class="text-gray-700 font-bold">Material:</dt>
                    <dd class="text-gray-800">{{ $loan->material->title }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-700 font-bold">Student:</dt>
                    <dd class="text-gray-800">{{ $loan->usuario->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-700 font-bold">Loan Date:</dt>
                    <dd class="text-gray-800">{{ $loan->created_at->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-700 font-bold">Due Date:</dt>
                    <dd class="text-gray-800 {{ $loan->isOverdue() ? 'text-red-600 font-bold' : '' }}">
                        {{ $loan->fecha_devolucion_esperada->format('M d, Y') }}
                        @if ($loan->isOverdue())
                            <span class="text-red-600 text-sm">({{ $loan->daysOverdue() }} days overdue)</span>
                        @endif
                    </dd>
                </div>
                @if ($loan->isOverdue())
                    <div class="flex justify-between pt-3 border-t border-blue-200">
                        <dt class="text-red-600 font-bold">Fine Amount:</dt>
                        <dd class="text-red-600 font-bold text-lg">${{ number_format($loan->calculateFine(), 2) }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        @if ($loan->isOverdue())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>This loan is overdue.</strong> A fine of <strong>${{ number_format($loan->calculateFine(), 2) }}</strong> 
                will be automatically generated upon return.
            </div>
        @endif

        <!-- Return Form -->
        <form action="{{ route('loans.return', $loan) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Condition -->
            <div>
                <label for="condicion" class="block text-gray-700 font-bold mb-2">Material Condition</label>
                <select name="condicion" id="condicion" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Select condition</option>
                    <option value="excelente" {{ old('condicion') === 'excelente' ? 'selected' : '' }}>Excellent</option>
                    <option value="bueno" {{ old('condicion') === 'bueno' ? 'selected' : '' }}>Good</option>
                    <option value="regular" {{ old('condicion') === 'regular' ? 'selected' : '' }}>Fair</option>
                    <option value="dañado" {{ old('condicion') === 'dañado' ? 'selected' : '' }}>Damaged</option>
                </select>
                @error('condicion')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notas_devolucion" class="block text-gray-700 font-bold mb-2">Return Notes (Optional)</label>
                <textarea name="notas_devolucion" id="notas_devolucion" rows="4" 
                          class="w-full border border-gray-300 rounded px-3 py-2"
                          placeholder="Any damages or issues with the material...">{{ old('notas_devolucion') }}</textarea>
                @error('notas_devolucion')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-check mr-2"></i> Confirm Return
                </button>
                <a href="{{ route('loans.show', $loan) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-block">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
