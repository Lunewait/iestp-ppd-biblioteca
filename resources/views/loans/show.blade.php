@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Loan Details</h1>
                <p class="text-gray-600">{{ $loan->material->title }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-white text-sm font-bold
                {{ $loan->status === 'active' ? 'bg-green-500' : 
                   ($loan->status === 'returned' ? 'bg-blue-500' : 'bg-red-500') }}">
                {{ ucfirst($loan->status) }}
            </span>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($loan->isOverdue())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                This loan is <strong>OVERDUE</strong>. A fine will be generated upon return.
            </div>
        @endif

        <div class="grid grid-cols-2 gap-6 mb-6">
            <!-- Loan Information -->
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-4">Loan Information</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-gray-600 font-bold">Loan ID:</dt>
                        <dd class="text-gray-800">{{ $loan->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Loan Date:</dt>
                        <dd class="text-gray-800">{{ $loan->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Due Date:</dt>
                        <dd class="text-gray-800">
                            {{ $loan->fecha_devolucion_esperada->format('M d, Y') }}
                            @if ($loan->isOverdue())
                                <span class="text-red-600 font-bold ml-2">(OVERDUE)</span>
                            @endif
                        </dd>
                    </div>
                    @if ($loan->fecha_devolucion)
                        <div>
                            <dt class="text-gray-600 font-bold">Return Date:</dt>
                            <dd class="text-gray-800">{{ $loan->fecha_devolucion->format('M d, Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- User Information -->
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-4">Student Information</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-gray-600 font-bold">Name:</dt>
                        <dd class="text-gray-800">{{ $loan->usuario->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Email:</dt>
                        <dd class="text-gray-800">{{ $loan->usuario->institutional_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Active Loans:</dt>
                        <dd class="text-gray-800">{{ $loan->usuario->prestamos()->where('status', 'active')->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-600 font-bold">Pending Fines:</dt>
                        <dd class="text-gray-800">${{ $loan->usuario->multas()->where('status', 'pendiente')->sum('monto') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Material Details -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Material Information</h2>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-gray-600 font-bold">Title:</dt>
                    <dd class="text-gray-800">{{ $loan->material->title }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-bold">Code:</dt>
                    <dd class="text-gray-800">{{ $loan->material->code }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-bold">Author:</dt>
                    <dd class="text-gray-800">{{ $loan->material->author }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-bold">Type:</dt>
                    <dd class="text-gray-800">{{ ucfirst($loan->material->type) }}</dd>
                </div>
            </dl>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            @if ($loan->status === 'active' && auth()->user()->hasPermissionTo('return_loan'))
                <a href="{{ route('loans.return-form', $loan) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-undo mr-2"></i> Return Loan
                </a>
            @endif
            <a href="{{ route('loans.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Loans
            </a>
        </div>
    </div>
</div>
@endsection
