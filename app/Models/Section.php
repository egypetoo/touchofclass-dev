<?php

namespace App\Models;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Helper\Path;
use App\Models\Concerns\ReplacesCallShortcodeInContent;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Section extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, ReplacesCallShortcodeInContent;

    protected $fillable = [
        'organization_id',
        'type',
        'page',
        'title',
        'second_title',
        'sub_title',
        'alt_image',
        'text',
        'button_text',
        'button_link_url',
        'order',
        'is_active',
    ];

    public array $translatable = [
        'title',
        'second_title',
        'sub_title',
        'text',
        'button_text',
    ];

    // Relations
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Scope the query to the given type.
     */
    #[Scope]
    protected function type(Builder $query, SectionTypeEnum|string $type): void
    {
        $query->where('type', $type);
    }

    #[Scope]
    protected function page(Builder $query, PageEnum|string $page): void
    {
        $query->where('page', $page);
    }

    #[Scope]
    protected function IsActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function getTextAttribute($text)
    {
        return $this->replaceCallShortcode($text);
    }

    /**
     * Title with admin-entered line breaks rendered as <br>.
     */
    public function titleHtml(): HtmlString
    {
        return new HtmlString(nl2br(e((string) $this->title), false));
    }

    public function titleLines(): array
    {
        $lines = preg_split('/\r\n|\r|\n/u', trim((string) $this->title)) ?: [];

        return array_values(array_filter(
            $lines,
            fn (string $line): bool => trim($line) !== '',
        ));
    }

    /**
     * Split a statistic title such as "30+" into its counter number and suffix.
     *
     * @return array{number: int, suffix: string}
     */
    public function statisticParts(): array
    {
        preg_match('/(\d+)(.*)/u', (string) $this->title, $matches);

        return [
            'number' => (int) ($matches[1] ?? 0),
            'suffix' => trim($matches[2] ?? ''),
        ];
    }

    public function imageUrl(string $designImagePath): string
    {
        return Path::mediaOrDesign($this->getFirstMediaUrl('sections') ?: null, $designImagePath, allowOversized: true);
    }
}
