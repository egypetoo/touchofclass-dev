<?php

namespace App\Models;

use App\Models\Concerns\ReplacesCallShortcodeInContent;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Spatie\Translatable\HasTranslations;

class ProductTab extends Model
{
    use HasTranslations, ReplacesCallShortcodeInContent;

    protected $fillable = [
        'product_id',
        'title',
        'content',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public array $translatable = [
        'title',
        'content',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getContentHtmlAttribute(): HtmlString
    {
        if (blank($this->content)) {
            return new HtmlString('');
        }

        $html = RichContentRenderer::make($this->content)->toHtml();

        return new HtmlString($this->replaceCallShortcode($html));
    }
}
