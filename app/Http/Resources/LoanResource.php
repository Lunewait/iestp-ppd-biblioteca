<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('usuario')),
            'material_id' => $this->material_id,
            'material' => new MaterialResource($this->whenLoaded('material')),
            'fecha_prestamo' => $this->fecha_prestamo,
            'fecha_devolucion_esperada' => $this->fecha_devolucion_esperada,
            'fecha_devolucion_actual' => $this->fecha_devolucion_actual,
            'status' => $this->status,
            'is_overdue' => $this->isOverdue(),
            'notas' => $this->notas,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
