<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'price'      => $this->price,
            'procedure'  => new ProcedureResource($this->whenLoaded('procedure')),
        ];
    }
}
