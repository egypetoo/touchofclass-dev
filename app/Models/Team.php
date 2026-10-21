<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Team extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'name',
        'title',
        'bio',
        'is_active',
        'order',
    ];

    public array $translatable = [
        'name',
        'title',
        'bio',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function imageMedia(): ?Media
    {
        return $this->getFirstMedia('teams');
    }

    public function imageUrl(): ?string
    {
        $media = $this->imageMedia();

        return $media ? $media->getUrl() : null;
    }

    #[Scope]
    protected function isActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
