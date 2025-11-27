<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create_material');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:fisico,digital,hibrido',
            'code' => 'required|unique:materials,code|string|max:50',
            'keywords' => 'nullable|string|max:500',
            'isbn' => 'nullable|string|max:20',
            'stock' => 'nullable|integer|min:0',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'location' => 'nullable|string|max:255',
            'url' => 'nullable|url',
            'file_type' => 'nullable|string|max:50',
            'downloadable' => 'nullable|boolean',
            'license' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio',
            'author.required' => 'El autor es obligatorio',
            'type.required' => 'El tipo de material es obligatorio',
            'code.required' => 'El código del material es obligatorio',
            'code.unique' => 'Este código ya existe',
        ];
    }
}
