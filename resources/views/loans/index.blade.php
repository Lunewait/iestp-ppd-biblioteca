@extends('layouts.app')

@section('title', 'Historial de Préstamos')

@section('content')
    <div class="mb-6">
        {{-- Header removed to avoid duplication with Livewire component --}}
    </div>

    <!-- Componente Livewire -->
    <livewire:loans-list />

    <!-- Componentes de soporte -->
    <livewire:notification-toast />
@endsection
