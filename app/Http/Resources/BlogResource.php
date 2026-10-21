<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'image' => $this->getFirstMediaUrl('blogs'),
            'alt_image' => $this->alt_image,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'published_at' => Carbon::parse($this->published_at)->format('Y-M-d'),
            'slug' => [
                'en' => $this->getTranslation('slug', 'en'),
                'ar' => $this->getTranslation('slug', 'ar'),
            ],
            'related_blogs' => $this->when(
                isset($this->resource['relatedBlogs']),
                fn () => BlogResource::collection($this['relatedBlogs']),
            ),
        ];
    }
}
