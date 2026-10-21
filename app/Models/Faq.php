<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasTranslations;

    protected $fillable = [
        'question',
        'answer',
        'sort_order',
        'is_active',
    ];

    public array $translatable = [
        'question',
        'answer',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
