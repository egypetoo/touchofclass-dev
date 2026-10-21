<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class GalleryImage extends Model implements HasMedia
{
    use HasTranslations  , InteractsWithMedia;

    protected $table = 'gallery_images';

    protected $fillable = [
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    public array $translatable = ['title', 'description'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
