<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'document'   => $this->document,
            'email'      => $this->email,
            'type_user'  => $this->type_user,
            'birth'      => $this->birth?->toDateString(),
            'photo'      => $this->photo,
            'state'      => $this->state,
            'created_at' => $this->created_at,
        ];
    }
}
