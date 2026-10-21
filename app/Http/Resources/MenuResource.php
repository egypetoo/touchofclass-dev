<?php

namespace App\Http\Resources;

use App\Support\MenuUrlResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            // 'route_name' => $this->route_name,
            // 'url' => MenuUrlResolver::resolve($this->route_name),
            'children' => $this->when(
                $this->relationLoaded('children'),
                fn () => MenuResource::collection(
                    $this->children
                        ->where('is_active', true)
                        ->sortBy('order')
                        ->values()
                ),
                []
            ),
        ];
    }
}
