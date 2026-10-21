<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Redirect extends Model
{
    protected $fillable = [
        'source',
        'target',
        'status_code',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        $forget = fn () => Cache::forget('redirects.map');
        static::saved($forget);
        static::deleted($forget);
    }
}
