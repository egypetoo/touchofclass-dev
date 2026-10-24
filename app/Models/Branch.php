<?php

namespace App\Models;

use App\Enums\BranchTypeEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Branch extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('branches')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    protected $fillable = [
        'organization_id',
        'name',
        'type',
        'address',
        'map_url',
        'map_embed',
        'phone_1_country_code',
        'phone_1',
        'phone_2_country_code',
        'phone_2',
        'working_hours',
        'email',
        'is_active',
        'order',
    ];

    public array $translatable = [
        'name',
        'address',
        'working_hours',
    ];

    protected static function booted(): void
    {
        $forget = function (): void {
            Cache::forget('main_branch');
            Cache::forget('branches');
        };

        static::saved($forget);
        static::deleted($forget);
    }

    protected function casts(): array
    {
        return [
            'type' => BranchTypeEnum::class,
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function mainImageMedia(): ?Media
    {
        return $this->getFirstMedia('branches');
    }

    public function mainImageUrl(): ?string
    {
        $media = $this->mainImageMedia();

        return $media ? $media->getUrl() : null;
    }

    /**
     * HTML iframe for maps. Accepts a full iframe snippet or a bare embed URL in map_embed.
     */
    public function mapEmbedHtml(string $iframeClass = ''): string
    {
        $embed = trim((string) $this->map_embed);
        $classAttr = $iframeClass !== '' ? ' class="'.e($iframeClass).'"' : '';
        $title = e((string) $this->name);

        if ($embed !== '' && str_contains(strtolower($embed), '<iframe')) {
            if ($iframeClass !== '' && ! str_contains($embed, $iframeClass)) {
                return (string) preg_replace(
                    '/<iframe\b/i',
                    '<iframe class="'.e($iframeClass).'"',
                    $embed,
                    1
                );
            }

            return $embed;
        }

        $src = $embed;

        if ($src === '' || ! str_starts_with($src, 'http')) {
            $query = $this->address ?: $this->name;
            $src = 'https://www.google.com/maps?q='.urlencode((string) $query).'&output=embed';
        }

        return '<iframe'.$classAttr
            .' src="'.e($src).'"'
            .' loading="lazy"'
            .' referrerpolicy="no-referrer-when-downgrade"'
            .' title="'.$title.'"'
            .' allowfullscreen></iframe>';
    }

    #[Scope]
    protected function isActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    #[Scope]
    protected function type(Builder $query, BranchTypeEnum|string $type): void
    {
        $query->where('type', $type);
    }
}
