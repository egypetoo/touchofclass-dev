<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceMetric extends Model
{
    protected $fillable = [
        'metric',
        'value',
        'rating',
        'page',
        'user_agent',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
        ];
    }
}
