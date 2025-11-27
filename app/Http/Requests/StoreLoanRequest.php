<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create_loan');
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'material_id' => 'required|exists:materials,id',
            'fecha_devolucion_esperada' => 'required|date|after:today',
            'notas' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio',
            'material_id.required' => 'El material es obligatorio',
            'fecha_devolucion_esperada.required' => 'La fecha de devolución es obligatoria',
            'fecha_devolucion_esperada.after' => 'La fecha debe ser posterior a hoy',
        ];
    }
}
