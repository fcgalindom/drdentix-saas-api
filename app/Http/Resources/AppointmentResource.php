<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day?->toDateString(),
            'hour' => $this->hour,
            'state' => $this->state,
            'pay' => $this->pay,
            'type_state' => $this->type_state,
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'dentist_procedure' => new DentistProcedureResource($this->whenLoaded('dentistProcedure')),
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
        ];
    }
}
