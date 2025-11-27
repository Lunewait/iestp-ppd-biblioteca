@extends('layouts.app')

@section('title', 'Catálogo de Materiales')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold mb-4">📚 Catálogo de Materiales</h1>
    </div>

    <!-- Componente Livewire -->
    <livewire:materials-list />

    @can('create_material')
        <div class="mt-6 flex justify-end">
            <a href="{{ route('materials.create') }}" class="px-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-lg">
                <i class="fas fa-plus"></i> Nuevo Material
            </a>
        </div>
    @endcan

    <!-- Componentes de soporte -->
    <livewire:notification-toast />
    <livewire:material-detail-modal />
@endsection
