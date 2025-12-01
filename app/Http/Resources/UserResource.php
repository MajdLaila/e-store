<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'avatar'     => $this->avatar ? url($this->avatar) : null,
            'is_active'  => (bool) $this->is_active,
            'is_admin'   => (bool) $this->is_admin,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
