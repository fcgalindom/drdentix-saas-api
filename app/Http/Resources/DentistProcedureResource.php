<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DentistProcedureResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'dentist' => new DentistResource($this->whenLoaded('dentist')),
            'procedure' => new ProcedureResource($this->whenLoaded('procedure')),
        ];
    }
}
