<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'desc' => $this->desc,
            'location' => $this->location,
            'date' => $this->date,
            'available_seats' => $this->available_seats,
            // 'category_id' => new CategoryResource($this->category->id),
            'category_id' => $this->category_id,
            'images' => $this->getMedia('event_images')->map(function ($image) {
                return $image->getFullUrl();
            })
        ];
    }
}
