<?php

namespace App\Models;

use App\Models\Concerns\ReplacesCallShortcodeInContent;
use App\Traits\HasLocalizedUrl;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Project extends Model implements HasMedia
{
    use HasLocalizedUrl, HasSlug, HasTranslations, InteractsWithMedia, ReplacesCallShortcodeInContent;

    protected $fillable = [
        'project_category_id',
        'title',
        'slug',
        'short_text',
        'location',
        'icon',
        'completed_year',
        'area',
        'duration',
        'features',
        'text',
        'meta_title',
        'meta_desc',
        'is_active',
        'show_in_home',
        'order',
    ];

    public array $translatable = [
        'title',
        'slug',
        'short_text',
        'location',
        'area',
        'duration',
        'features',
        'text',
        'meta_title',
        'meta_desc',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_in_home' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    /**
     * @return list<string>
     */
    public function featureList(): array
    {
        $features = $this->features;

        if (is_string($features)) {
            $features = json_decode($features, true);
        }

        if (! is_array($features)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($feature) => is_string($feature) ? trim($feature) : '',
            $features
        )));
    }

    protected function localizedUrlPath(): string
    {
        return 'projects';
    }

    protected function slugFromAttribute(): string
    {
        return 'title';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function coverUrl(): ?string
    {
        return $this->getFirstMediaUrl('cover')
            ?: $this->getFirstMediaUrl('gallery')
            ?: null;
    }

    /**
     * @return Collection<int, string>
     */
    public function galleryLightboxUrls(): Collection
    {
        $urls = collect();

        $cover = $this->getFirstMediaUrl('cover');
        if (filled($cover)) {
            $urls->push($cover);
        }

        foreach ($this->getMedia('gallery') as $media) {
            $url = $media->getUrl();
            if (filled($url) && ! $urls->contains($url)) {
                $urls->push($url);
            }
        }

        return $urls;
    }

    public function mainImageMedia(): ?Media
    {
        return $this->getFirstMedia('cover') ?: $this->getFirstMedia('gallery');
    }

    public function getTextAttribute($text)
    {
        return $this->replaceCallShortcode($text);
    }

    #[Scope]
    protected function isActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function showInHome(Builder $query): void
    {
        $query->where('show_in_home', true);
    }
}
