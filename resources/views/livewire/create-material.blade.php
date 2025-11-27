<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Registrar Nuevo Material</h2>

        <!-- Validation Messages -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-red-800 mb-2">Por favor, corrije los siguientes errores:</h3>
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit="save" class="space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
                <input 
                    type="text" 
                    id="title"
                    wire:model="title"
                    placeholder="Ingrese el título del material"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                >
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Author -->
            <div>
                <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Autor *</label>
                <input 
                    type="text" 
                    id="author"
                    wire:model="author"
                    placeholder="Nombre del autor"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('author') border-red-500 @enderror"
                >
                @error('author')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- ISBN -->
            <div>
                <label for="isbn" class="block text-sm font-medium text-gray-700 mb-2">ISBN</label>
                <input 
                    type="text" 
                    id="isbn"
                    wire:model="isbn"
                    placeholder="978-84-376-0494-7"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea 
                    id="description"
                    wire:model="description"
                    rows="4"
                    placeholder="Describe el contenido del material"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                ></textarea>
            </div>

            <!-- Material Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Material *</label>
                <select 
                    id="type"
                    wire:model.live="type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                >
                    <option value="">Selecciona un tipo</option>
                    <option value="fisico">Físico</option>
                    <option value="digital">Digital</option>
                    <option value="hibrido">Híbrido</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Physical Material Fields -->
            @if($type === 'fisico' || $type === 'hibrido')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-4">
                    <h3 class="font-semibold text-blue-900">Información de Material Físico</h3>
                    
                    <!-- Publisher -->
                    <div>
                        <label for="publisher" class="block text-sm font-medium text-gray-700 mb-2">Editorial</label>
                        <input 
                            type="text" 
                            id="publisher"
                            wire:model="publisher"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Publication Year -->
                    <div>
                        <label for="publication_year" class="block text-sm font-medium text-gray-700 mb-2">Año de Publicación</label>
                        <input 
                            type="number" 
                            id="publication_year"
                            wire:model="publication_year"
                            min="1900" 
                            max="{{ date('Y') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Cantidad *</label>
                        <input 
                            type="number" 
                            id="quantity"
                            wire:model="quantity"
                            min="1"
                            placeholder="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantity') border-red-500 @enderror"
                        >
                        @error('quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Ubicación en la Biblioteca</label>
                        <input 
                            type="text" 
                            id="location"
                            wire:model="location"
                            placeholder="Ej: Estante A-3, Fila 2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                </div>
            @endif

            <!-- Digital Material Fields -->
            @if($type === 'digital' || $type === 'hibrido')
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 space-y-4">
                    <h3 class="font-semibold text-green-900">Información de Material Digital</h3>
                    
                    <!-- URL -->
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-2">URL de Acceso *</label>
                        <input 
                            type="url" 
                            id="url"
                            wire:model="url"
                            placeholder="https://ejemplo.com/recurso"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('url') border-red-500 @enderror"
                        >
                        @error('url')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Type -->
                    <div>
                        <label for="file_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Archivo</label>
                        <select 
                            id="file_type"
                            wire:model="file_type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Selecciona un tipo</option>
                            <option value="pdf">PDF</option>
                            <option value="epub">EPUB</option>
                            <option value="video">Video</option>
                            <option value="audio">Audio</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <!-- License -->
                    <div>
                        <label for="license" class="block text-sm font-medium text-gray-700 mb-2">Licencia</label>
                        <input 
                            type="text" 
                            id="license"
                            wire:model="license"
                            placeholder="Ej: Creative Commons"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                </div>
            @endif

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                <input 
                    type="text" 
                    id="category"
                    wire:model="category"
                    placeholder="Ej: Tecnología, Literatura"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4 pt-4">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                >
                    Registrar Material
                </button>
                <a 
                    href="{{ route('materials.index') }}"
                    class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 font-medium"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
