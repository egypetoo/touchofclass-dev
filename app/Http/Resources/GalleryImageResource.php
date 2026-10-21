<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryImageResource extends JsonResource
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
            'description' => $this->description,
            'image' => $this->getFirstMediaUrl('gallery_images'),
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'images' => $this->getMedia('gallery_images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'type' => $media->mime_type,
                ];
            }),
        ];
    }
}
