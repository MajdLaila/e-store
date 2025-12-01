<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'desc'        => $this->desc,
            'product_id'  => $this->product_id,
            'image'       => $this->image,
            'image_url'   => $this->image ? asset('storage/' . $this->image) : null,
            'product'     => $this->whenLoaded('product'),
            'created_at'  => $this->created_at,
        ];
    }
}
