<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'breadcrumb_section' => SectionResource::make($this['breadcrumb_section'] ?? null),
            'about_us_section' => SectionResource::make($this['about_us_section'] ?? null),
            'statistics_section' => SectionResource::collection($this['statistics_section'] ?? []),
            'values_section' => SectionResource::collection($this['values_section'] ?? []),
        ];
    }
}
