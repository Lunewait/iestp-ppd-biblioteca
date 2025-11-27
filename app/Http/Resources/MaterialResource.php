<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'author' => $this->author,
            'type' => $this->type,
            'code' => $this->code,
            'keywords' => $this->keywords,
            'physical' => new MaterialFisicoResource($this->whenLoaded('materialFisico')),
            'digital' => new MaterialDigitalResource($this->whenLoaded('materialDigital')),
            'is_available' => $this->isAvailable(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
