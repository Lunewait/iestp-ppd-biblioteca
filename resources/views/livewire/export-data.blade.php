<div class="space-y-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Exportar Datos</h3>
        
        <div class="space-y-3">
            <button wire:click="exportMaterialsCSV()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                📥 Descargar Materiales (CSV)
            </button>
            
            <button wire:click="exportLoansCSV()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                📥 Descargar Préstamos (CSV)
            </button>
        </div>
    </div>
</div>
