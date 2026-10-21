<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'sub_title' => $this->sub_title,
            'text' => $this->text,
            'image' => $this->getFirstMediaUrl('sections'),
            'alt_image' => $this->alt_image,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'button_text' => $this->button_text,
            'button_link_url' => $this->button_link_url,
        ];
    }
}
