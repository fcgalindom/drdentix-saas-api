<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'date_start'     => $this->date_start?->toDateString(),
            'date_end'       => $this->date_end?->toDateString(),
            'details'        => $this->details,
            'discount'       => $this->discount,
            'limit_patients' => $this->limit_patients,
            'status'         => $this->status,
        ];
    }
}
