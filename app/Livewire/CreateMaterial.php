<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\MaterialFisico;
use App\Models\MaterialDigital;
use Livewire\Component;
use Livewire\Attributes\Rule;

class CreateMaterial extends Component
{
    #[Rule('required|string|max:255')]
    public $title = '';

    #[Rule('required|string|max:255')]
    public $author = '';

    #[Rule('nullable|string')]
    public $description = '';

    #[Rule('required|in:fisico,digital,hibrido')]
    public $type = 'fisico';

    #[Rule('nullable|string')]
    public $isbn = '';

    #[Rule('nullable|numeric|min:1')]
    public $quantity = 1;

    #[Rule('nullable|string')]
    public $publisher = '';

    #[Rule('nullable|numeric|min:1900|max:2099')]
    public $publication_year = '';

    #[Rule('nullable|string')]
    public $location = '';

    #[Rule('nullable|string|url')]
    public $url = '';

    #[Rule('nullable|string')]
    public $file_type = '';

    #[Rule('nullable|string')]
    public $license = '';

    #[Rule('nullable|string')]
    public $category = '';

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:fisico,digital,hibrido',
            'isbn' => 'nullable|string',
            'quantity' => 'nullable|numeric|min:1',
            'publisher' => 'nullable|string',
            'publication_year' => 'nullable|numeric|min:1900|max:2099',
            'location' => 'nullable|string',
            'url' => ($this->type === 'digital' || $this->type === 'hibrido') ? 'required|string|url' : 'nullable|string|url',
            'file_type' => 'nullable|string',
            'license' => 'nullable|string',
            'category' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio',
            'author.required' => 'El autor es obligatorio',
            'type.required' => 'El tipo de material es obligatorio',
            'url.required' => 'La URL es obligatoria para materiales digitales',
            'url.url' => 'La URL debe ser válida',
        ];
    }

    public function save()
    {
        $this->authorize('create_material');
        
        $validated = $this->validate();

        $material = Material::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'category' => $validated['category'] ?? null,
        ]);

        if ($this->type === 'fisico' || $this->type === 'hibrido') {
            MaterialFisico::create([
                'material_id' => $material->id,
                'isbn' => $validated['isbn'] ?? null,
                'stock' => $validated['quantity'] ?? 1,
                'available' => $validated['quantity'] ?? 1,
                'publisher' => $validated['publisher'] ?? null,
                'publication_year' => $validated['publication_year'] ?? null,
                'location' => $validated['location'] ?? null,
            ]);
        }

        if ($this->type === 'digital' || $this->type === 'hibrido') {
            MaterialDigital::create([
                'material_id' => $material->id,
                'url' => $validated['url'] ?? null,
                'file_type' => $validated['file_type'] ?? null,
                'license' => $validated['license'] ?? null,
            ]);
        }

        session()->flash('success', 'Material creado exitosamente');
        
        return redirect()->route('materials.index');
    }

    public function render()
    {
        return view('livewire.create-material');
    }
}
