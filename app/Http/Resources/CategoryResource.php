<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'short_text' => $this->short_text,
            'text' => $this->text,
            'image' => $this->getFirstMediaUrl('categories'),
            'alt_image' => $this->alt_image,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'show_in_navbar' => $this->show_in_navbar,
            'show_in_footer' => $this->show_in_footer,
            'is_main_section' => $this->is_main_section,
            'images' => $this->getMedia('categories')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                ];
            }),
            'slug' => [
                'en' => $this->getTranslation('slug', 'en'),
                'ar' => $this->getTranslation('slug', 'ar'),
            ],
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
