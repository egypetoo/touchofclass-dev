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
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements HasMedia
{
    use HasLocalizedUrl, HasSlug, HasTranslations, InteractsWithMedia, ReplacesCallShortcodeInContent;

    protected $fillable = [
        'organization_id',
        'parent_id',
        'name',
        'short_text',
        'text',
        'slug',
        'meta_title',
        'meta_desc',
        'order',
        'is_active',
        'show_in_navbar',
        'show_in_footer',
        'is_main_section',
    ];

    public array $translatable = [
        'name',
        'short_text',
        'text',
        'slug',
        'meta_title',
        'meta_desc',
    ];

    protected function localizedUrlPath(): string
    {
        return 'categories';
    }

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
        'show_in_navbar' => 'boolean',
        'show_in_footer' => 'boolean',
        'is_main_section' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('navbar_categories');
            Cache::forget('footer_categories');
        });
        static::updated(function (): void {
            Cache::forget('navbar_categories');
            Cache::forget('footer_categories');
        });
        static::deleted(function (): void {
            Cache::forget('navbar_categories');
            Cache::forget('footer_categories');
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return list<int>
     */
    public function descendantIds(): array
    {
        $this->loadMissing('children');

        $ids = [];

        foreach ($this->children as $child) {
            $ids[] = (int) $child->id;
            $ids = array_merge($ids, $child->descendantIds());
        }

        return $ids;
    }

    /**
     * @return list<int>
     */
    public function branchIds(): array
    {
        return array_values(array_unique(array_merge([(int) $this->id], $this->descendantIds())));
    }

    public function isLeaf(): bool
    {
        if ($this->relationLoaded('children')) {
            return $this->children->isEmpty();
        }

        return ! $this->children()->exists();
    }

    public function hasGrandchildren(): bool
    {
        $this->loadMissing('children.children');

        return $this->children->contains(fn (Category $child): bool => $child->children->isNotEmpty());
    }

    #[Scope]
    protected function isActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function shownInNavbar(Builder $query): void
    {
        $query->where('show_in_navbar', true);
    }

    #[Scope]
    protected function inMainSection(Builder $query): void
    {
        $query->where('is_main_section', true);
    }

    #[Scope]
    protected function manufacturing(Builder $query): void
    {
        $query->whereKey(10);
    }

    public function isManufacturing(): bool
    {
        return $this->getKey() === 10;
    }

    public function getTextAttribute($text)
    {
        return $this->replaceCallShortcode($text);
    }
}
