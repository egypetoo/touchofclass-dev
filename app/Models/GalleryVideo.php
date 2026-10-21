<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class GalleryVideo extends Model implements HasMedia
{
    use HasTranslations , InteractsWithMedia;

    protected $table = 'gallery_videos';

    protected $fillable = [
        'service_id',
        'title',
        'description',
        'video_url',
        'sort_order',
        'is_active',
    ];

    public array $translatable = ['title', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForService(Builder $query, ?int $serviceId): Builder
    {
        if ($serviceId === null) {
            return $query;
        }

        return $query->where('service_id', $serviceId);
    }
}
