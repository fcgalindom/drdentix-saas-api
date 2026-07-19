<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DentistResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'city'       => $this->city,
            'user'       => new UserResource($this->whenLoaded('user')),
            'procedures' => ProcedureResource::collection($this->whenLoaded('procedures')),
            'schedules'  => ScheduleResource::collection($this->whenLoaded('schedules')),
        ];
    }
}
