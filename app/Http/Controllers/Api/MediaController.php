<?php

namespace App\Http\Controllers\Api;

use App\Enums\PageEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryImageResource;
use App\Http\Resources\GalleryVideoResource;
use App\Models\GalleryImage;
use App\Models\GalleryVideo;
use App\Models\Section;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function galleryImages()
    {
        $galleryImages = GalleryImage::isActive()
            ->orderBy('sort_order', 'asc')
            ->get();

        return GalleryImageResource::collection($galleryImages);
    }

    public function galleryVideos(Request $request)
    {

        $data['breadcrumb'] = Section::isActive()
            ->where('page', PageEnum::GALLERY_VIDEOS->value)
            ->first();

        $serviceId = $request->integer('service_id') ?: null;

        $data['gallery_videos'] = GalleryVideo::query()
            ->with('service')
            ->isActive()
            ->forService($serviceId)
            ->orderBy('sort_order', 'asc')
            ->get();

        return GalleryVideoResource::make($data);
    }
}
