<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'main_image' => $this->getFirstMediaUrl('products'),
            'images' => $this->getMedia('products')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                ];
            }),
            'slug' => [
                'en' => $this->getTranslation('slug', 'en'),
                'ar' => $this->getTranslation('slug', 'ar'),
            ],
            'category' => new CategoryResource($this->whenLoaded('category')),

        ];
    }
}
