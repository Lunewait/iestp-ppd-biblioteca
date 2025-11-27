<div class="space-y-8">
    <!-- Hero Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-900 to-blue-700 text-white shadow-xl">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div
            class="relative z-10 px-8 py-16 md:py-20 text-center md:text-left md:flex md:items-center md:justify-between">
            <div class="md:w-2/3">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-4 tracking-tight">Descubre y Disfruta de <br />Miles
                    de Libros</h1>
                <p class="text-blue-100 text-lg md:text-xl mb-8 max-w-2xl">
                    Accede a una amplia colección de libros, gestionados de forma rápida y eficiente desde nuestra
                    plataforma.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#catalogo"
                        class="px-8 py-3 bg-white text-blue-900 rounded-full font-bold hover:bg-blue-50 transition shadow-lg transform hover:-translate-y-1">
                        Explorar Libros
                    </a>
                    @can('create_material')
                        <a href="{{ route('materials.create') }}"
                            class="px-8 py-3 bg-blue-600 text-white border border-blue-400 rounded-full font-bold hover:bg-blue-500 transition shadow-lg transform hover:-translate-y-1">
                            Nuevo Material
                        </a>
                    @endcan
                </div>
            </div>
            <div class="hidden md:block md:w-1/3 relative">
                <!-- Decorative elements mimicking books -->
                <div
                    class="absolute right-0 top-1/2 transform -translate-y-1/2 rotate-12 w-48 h-64 bg-blue-400 rounded-lg shadow-2xl opacity-80">
                </div>
                <div
                    class="absolute right-8 top-1/2 transform -translate-y-1/2 -rotate-6 w-48 h-64 bg-white rounded-lg shadow-2xl z-10 flex items-center justify-center">
                    <span class="text-blue-900 font-bold text-2xl">IESTP</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div id="catalogo" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sticky top-4 z-20">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" placeholder="Buscar por título, autor, código..." wire:model.live="search"
                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <div class="md:col-span-3">
                <select wire:model.live="filterType"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition cursor-pointer">
                    <option value="all">Todos los tipos</option>
                    <option value="fisico">📚 Libros Físicos</option>
                    <option value="digital">💻 Digitales</option>
                    <option value="hibrido">🔄 Híbridos</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <select wire:model.live="sortBy"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition cursor-pointer">
                    <option value="created_at">✨ Más Recientes</option>
                    <option value="title">🔤 Título (A-Z)</option>
                    <option value="author">👤 Autor (A-Z)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Materials Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($materials as $material)
            <div
                class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col hover:-translate-y-1">
                <!-- Cover Placeholder -->
                <div class="h-48 bg-gray-100 relative overflow-hidden group-hover:opacity-90 transition">
                    @if($material->type === 'digital')
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-green-400 to-teal-500 flex items-center justify-center">
                            <span class="text-white text-5xl">PDF</span>
                        </div>
                    @elseif($material->type === 'hibrido')
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center">
                            <span class="text-white text-5xl">🔄</span>
                        </div>
                    @else
                        <!-- Random color gradient based on ID for visual variety -->
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <span
                                class="text-white text-4xl font-serif font-bold opacity-50">{{ substr($material->title, 0, 1) }}</span>
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="absolute top-3 right-3">
                        @if($material->isAvailable())
                            <span
                                class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded shadow-sm">Disponible</span>
                        @else
                            <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded shadow-sm">Agotado</span>
                        @endif
                    </div>
                </div>

                <div class="p-5 flex-1 flex flex-col">
                    <div class="mb-2">
                        <span
                            class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $material->code }}</span>
                    </div>
                    <h3
                        class="text-lg font-bold text-gray-900 mb-1 leading-tight group-hover:text-blue-600 transition-colors">
                        <a href="{{ route('materials.show', $material) }}">
                            {{ Str::limit($material->title, 40) }}
                        </a>
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $material->author }}</p>

                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-medium px-2 py-1 rounded bg-gray-100 text-gray-600">
                            {{ ucfirst($material->type) }}
                        </span>
                        <a href="{{ route('materials.show', $material) }}"
                            class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Ver Detalles →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <div class="inline-block p-4 rounded-full bg-gray-50 mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No se encontraron materiales</h3>
                <p class="text-gray-500 mt-1">Intenta con otros términos de búsqueda.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-8">
        {{ $materials->links() }}
    </div>
</div>