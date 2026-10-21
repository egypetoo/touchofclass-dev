<?php

namespace App\Models;

use App\Enums\ClientSectorEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Partner extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'organization_id',
        'name',
        'url',
        'sector',
        'show_in_home',
        'is_active',
        'order',
    ];

    public array $translatable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_in_home' => 'boolean',
            'sector' => ClientSectorEnum::class,
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function imageMedia(): ?Media
    {
        return $this->getFirstMedia('partners');
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

    #[Scope]
    protected function showInHome(Builder $query): void
    {
        $query->where('show_in_home', true);
    }
}
