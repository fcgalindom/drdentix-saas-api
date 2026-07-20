<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'active_principle' => $this->active_principle,
            'concentration' => $this->concentration,
            'amount' => $this->amount,
            'pharmaceutical_form' => $this->pharmaceutical_form,
            'commercial_presentation' => $this->commercial_presentation,
            'medication_unit' => $this->medication_unit,
            'batch' => $this->batch,
            'health_register_invima' => $this->health_register_invima,
            'expiration_date' => $this->expiration_date?->toDateString(),
            'semaphore' => $this->semaphore,
            'date_of_admission' => $this->date_of_admission?->toDateString(),
        ];
    }
}
