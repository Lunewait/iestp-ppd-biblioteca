<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create_fine');
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'prestamo_id' => 'nullable|exists:prestamos,id',
            'monto' => 'required|numeric|min:0.01',
            'razon' => 'required|string|max:500',
            'status' => 'nullable|in:pendiente,pagada,condonada',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio',
            'monto.required' => 'El monto es obligatorio',
            'monto.min' => 'El monto debe ser mayor a 0',
            'razon.required' => 'La razón es obligatoria',
        ];
    }
}
