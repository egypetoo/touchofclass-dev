<?php

namespace App\Support\Media;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class ModuleScopedMediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->basePath($media).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->basePath($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->basePath($media).'/responsive-images/';
    }

    protected function basePath(Media $media): string
    {
        $prefix = trim((string) config('media-library.prefix', ''), '/');

        $module = Str::slug(Str::lower(Str::plural(class_basename($media->model_type))));

        $segment = $module.'/'.$media->model_id;

        return $prefix !== '' ? $prefix.'/'.$segment : $segment;
    }
}
