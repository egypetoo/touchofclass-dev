<?php

namespace App\Models;

use App\Models\Concerns\ReplacesCallShortcodeInContent;
use App\Traits\HasLocalizedUrl;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Service extends Model implements HasMedia
{
    use HasLocalizedUrl, HasSlug, HasTranslations, InteractsWithMedia, ReplacesCallShortcodeInContent;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('services')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    protected $fillable = [
        'organization_id',
        'parent_id',
        'title',
        'label',
        'slug',
        'meta_title',
        'meta_desc',
        'short_text',
        'text',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public array $translatable = [
        'title',
        'label',
        'slug',
        'meta_title',
        'meta_desc',
        'short_text',
        'text',
    ];

    protected function localizedUrlPath(): string
    {
        return 'services';
    }

    protected function slugFromAttribute(): string
    {
        return 'title';
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('order');
    }

    public function galleryVideos(): HasMany
    {
        return $this->hasMany(GalleryVideo::class);
    }

    public function hasChildren(): bool
    {
        if ($this->relationLoaded('children')) {
            return $this->children->isNotEmpty();
        }

        return $this->children()->exists();
    }

    public function mainImageMedia(): ?Media
    {
        return $this->getFirstMedia('services');
    }

    public function mainImageUrl(): ?string
    {
        $media = $this->mainImageMedia();

        return $media ? $media->getUrl() : null;
    }

    /**
     * Photos shown in the details slider and home showcase: every image after the first (card) image,
     * or the card image alone when no extra photos were uploaded.
     *
     * @return list<string>
     */
    public function galleryImageUrls(): array
    {
        $urls = $this->getMedia('services')
            ->map(fn (Media $media): string => $media->getUrl())
            ->values();

        return $urls->count() > 1
            ? $urls->slice(1)->values()->all()
            : $urls->all();
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
    protected function roots(Builder $query): void
    {
        $query->whereNull('parent_id');
    }
}
