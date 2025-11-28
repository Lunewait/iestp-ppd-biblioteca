<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialDigital;
use App\Models\MaterialFisico;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MaterialController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of materials.
     */
    public function index(Request $request)
    {
        $query = Material::query();

        // Search by title, author, or code
        if ($request->search) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        }

        // Filter by type
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $materials = $query->with(['materialFisico', 'materialDigital'])
            ->paginate(15);

        return view('materials.index', compact('materials'));
    }

    /**
     * Show the form for creating a new material.
     */
    public function create()
    {
        $this->authorize('create_material');

        return view('materials.create');
    }

    /**
     * Store a newly created material in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create_material');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:fisico,digital,hibrido',
            'code' => 'required|unique:materials,code',
            'keywords' => 'nullable|string',
            // Validaciones condicionales
            'url' => 'required_if:type,digital,hibrido|nullable|url',
            'stock' => 'required_if:type,fisico,hibrido|nullable|integer|min:0',
        ]);

        $material = Material::create($validated);

        if ($request->type === 'fisico' || $request->type === 'hibrido') {
            MaterialFisico::create([
                'material_id' => $material->id,
                'isbn' => $request->isbn,
                'stock' => $request->stock ?? 0,
                'available' => $request->stock ?? 0,
                'publisher' => $request->publisher,
                'publication_year' => $request->publication_year,
                'location' => $request->location,
            ]);
        }

        if ($request->type === 'digital' || $request->type === 'hibrido') {
            MaterialDigital::create([
                'material_id' => $material->id,
                'url' => $request->url,
                'downloadable' => $request->has('downloadable'),
                'file_type' => $request->file_type,
                'license' => $request->license,
            ]);
        }

        return redirect()->route('materials.show', $material)
            ->with('success', 'Material creado exitosamente');
    }

    /**
     * Display the specified material.
     */
    public function show(Material $material)
    {
        $material->load(['materialFisico', 'materialDigital', 'prestamos', 'reservas']);

        return view('materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified material.
     */
    public function edit(Material $material)
    {
        $this->authorize('edit_material');

        return view('materials.edit', compact('material'));
    }

    /**
     * Update the specified material in storage.
     */
    public function update(Request $request, Material $material)
    {
        $this->authorize('edit_material');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        $material->update($validated);

        return redirect()->route('materials.show', $material)
            ->with('success', 'Material actualizado exitosamente');
    }

    /**
     * Remove the specified material from storage.
     */
    public function destroy(Material $material)
    {
        $this->authorize('delete_material');

        $material->delete();

        return redirect()->route('materials.index')
            ->with('success', 'Material eliminado exitosamente');
    }
}
