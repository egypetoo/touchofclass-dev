<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Spatie\Translatable\HasTranslations;

class Menu extends Model
{
    use HasTranslations;

    protected $fillable = [
        'organization_id',
        'title',
        'parent_id',
        'order',
        'is_active',
        'is_navbar',
        'is_footer',
        'route_name',
    ];

    public array $translatable = [
        'title',
    ];

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('main_menu');
            Cache::forget('footer_navigation');
        });
        static::updated(function (): void {
            Cache::forget('main_menu');
            Cache::forget('footer_navigation');
        });

        static::deleted(function (): void {
            Cache::forget('main_menu');
            Cache::forget('footer_navigation');
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    #[Scope]
    protected function isActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function href(): string
    {
        $name = (string) $this->route_name;

        if ($name === '' || $name === '#' || ! Route::has($name)) {
            return '#';
        }

        return route($name === 'products.index' ? 'categories.index' : $name);
    }

    public function isCurrent(): bool
    {
        $pattern = match ($this->route_name) {
            'services.index' => 'services.*',
            'products.index', 'categories.index' => ['products.*', 'categories.*'],
            'news.index' => 'news.*',
            'projects.index' => 'projects.*',
            'partners.index' => 'partners.*',
            '#', null, '' => null,
            default => $this->route_name,
        };

        return $pattern !== null && request()->routeIs($pattern);
    }

    public function showsProductMenu(): bool
    {
        return $this->route_name === 'products.index';
    }
}
