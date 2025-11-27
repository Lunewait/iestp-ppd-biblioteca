<div>
    @if($showModal && $material)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-96 overflow-y-auto">
                <!-- Header -->
                <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-white">Detalles del Material</h2>
                    <button wire:click="closeModal()" class="text-white hover:text-gray-200 text-2xl">✕</button>
                </div>

                <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Title and Author -->
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $material->title }}</h3>
                <p class="text-gray-600 text-lg">por {{ $material->author }}</p>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Código</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $material->code ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tipo</p>
                    <p class="text-lg font-semibold">
                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                            @if($material->type === 'fisico') bg-blue-100 text-blue-800
                            @elseif($material->type === 'digital') bg-green-100 text-green-800
                            @else bg-purple-100 text-purple-800
                            @endif">
                            {{ ucfirst($material->type) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Description -->
            @if($material->description)
            <div>
                <p class="text-sm text-gray-600 mb-2">Descripción</p>
                <p class="text-gray-700 text-justify">{{ $material->description }}</p>
            </div>
            @endif

            <!-- Physical Details -->
            @if($material->materialFisico)
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-bold text-blue-900 mb-3">Información Física</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    @if($material->materialFisico->isbn)
                    <div>
                        <p class="text-gray-600">ISBN</p>
                        <p class="font-semibold">{{ $material->materialFisico->isbn }}</p>
                    </div>
                    @endif
                    @if($material->materialFisico->publisher)
                    <div>
                        <p class="text-gray-600">Editorial</p>
                        <p class="font-semibold">{{ $material->materialFisico->publisher }}</p>
                    </div>
                    @endif
                    @if($material->materialFisico->publication_year)
                    <div>
                        <p class="text-gray-600">Año</p>
                        <p class="font-semibold">{{ $material->materialFisico->publication_year }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-gray-600">Stock</p>
                        <p class="font-semibold">{{ $material->materialFisico->stock }} unidades</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Disponibles</p>
                        <p class="font-semibold text-green-600">{{ $material->materialFisico->available }} unidades</p>
                    </div>
                    @if($material->materialFisico->location)
                    <div>
                        <p class="text-gray-600">Ubicación</p>
                        <p class="font-semibold">{{ $material->materialFisico->location }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Digital Details -->
            @if($material->materialDigital)
            <div class="bg-green-50 p-4 rounded-lg">
                <h4 class="font-bold text-green-900 mb-3">Información Digital</h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    @if($material->materialDigital->url)
                    <div class="col-span-2">
                        <p class="text-gray-600">URL de Acceso</p>
                        <a href="{{ $material->materialDigital->url }}" target="_blank" class="text-blue-600 underline font-semibold truncate">
                            {{ $material->materialDigital->url }}
                        </a>
                    </div>
                    @endif
                    @if($material->materialDigital->file_type)
                    <div>
                        <p class="text-gray-600">Tipo de Archivo</p>
                        <p class="font-semibold">{{ strtoupper($material->materialDigital->file_type) }}</p>
                    </div>
                    @endif
                    @if($material->materialDigital->license)
                    <div>
                        <p class="text-gray-600">Licencia</p>
                        <p class="font-semibold">{{ $material->materialDigital->license }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Category -->
            @if($material->category)
            <div>
                <p class="text-sm text-gray-600 mb-2">Categoría</p>
                <span class="inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded-full font-semibold">
                    {{ $material->category }}
                </span>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
            <button wire:click="closeModal()" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-medium">
                Cerrar
            </button>
            <a href="{{ route('materials.show', $material) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Ver Completo
            </a>
        </div>
            </div>
        </div>
    @endif
</div>
