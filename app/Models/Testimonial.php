<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Testimonial extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'video_url',
        'sort_order',
        'is_active',
    ];

    public function imageMedia(): ?Media
    {
        return $this->getFirstMedia('testimonials');
    }

    public function imageUrl(): ?string
    {
        $media = $this->imageMedia();

        return $media ? $media->getUrl() : null;
    }

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
