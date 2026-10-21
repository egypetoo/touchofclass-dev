<?php

namespace App\Models;

use App\Models\Concerns\ReplacesCallShortcodeInContent;
use App\Traits\HasLocalizedUrl;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Blog extends Model implements HasMedia
{
    use HasLocalizedUrl, HasSlug, HasTranslations, InteractsWithMedia, ReplacesCallShortcodeInContent;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('blogs')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    protected $fillable = [
        'organization_id',
        'blog_category_id',
        'title',
        'excerpt',
        'content',
        'slug',
        'meta_title',
        'meta_desc',
        'order',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public array $translatable = [
        'title',
        'excerpt',
        'content',
        'slug',
        'meta_title',
        'meta_desc',
    ];

    protected function localizedUrlPath(): string
    {
        return 'blogs';
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function blogCategory(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class);
    }

    protected function slugFromAttribute(): string
    {
        return 'title';
    }

    public function getContentAttribute($content)
    {
        return $this->replaceCallShortcode($content);
    }

    #[Scope]
    protected function isActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
