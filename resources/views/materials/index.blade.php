@extends('layouts.app')

@section('title', 'Catálogo de Materiales')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold">📚 Catálogo de Materiales</h1>

        @can('export_materials')
            <a href="{{ route('materials.export') }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar a Excel
            </a>
        @endcan
    </div>

    <!-- Componente Livewire -->
    <livewire:materials-list />

    @can('create_material')
        <div class="mt-6 flex justify-end">
            <a href="{{ route('materials.create') }}"
                class="px-6 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-lg">
                <i class="fas fa-plus"></i> Nuevo Material
            </a>
        </div>
    @endcan

    <!-- Componentes de soporte -->
    <livewire:notification-toast />
    <livewire:material-detail-modal />
@endsection