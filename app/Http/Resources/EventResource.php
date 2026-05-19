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
        // return parent::toArray($request);
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'description'       => $this->description,
            'location'          => $this->location,
            'start_date'        => $this->start_date,
            'avilable_seats'    => $this->avilable_seats,
            'image'             => $this->getMedia("main_image")->map(function ($media) {
                return $media->getUrl();
            }),
            'category'          => new CategoryResource($this->whenLoaded('category')),

        ];
    }
}
