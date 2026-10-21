<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryVideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'breadcrumb' => new SectionResource($this['breadcrumb']),
            'gallery_videos' => $this['gallery_videos']->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'description' => $video->description,
                    'youtube_url' => $video->video_url,
                    'sort_order' => $video->sort_order,
                    'is_active' => $video->is_active,
                    'video' => $video->getFirstMediaUrl('gallery_videos'),
                    'thumbnail' => $video->getFirstMediaUrl('gallery_video_images'),
                ];
            }),
        ];
    }
}
