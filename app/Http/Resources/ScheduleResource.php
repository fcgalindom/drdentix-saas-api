<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'day'         => $this->day,
            'attend'      => $this->attend,
            'hour_start'  => $this->hour_start,
            'hour_end'    => $this->hour_end,
            'break'       => $this->break,
            'break_start' => $this->break_start,
            'break_end'   => $this->break_end,
        ];
    }
}
