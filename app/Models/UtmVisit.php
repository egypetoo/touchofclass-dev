<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UtmVisit extends Model
{
    protected $fillable = [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'path',
        'landing_page',
        'referrer',
        'session_id',
        'ip_hash',
        'user_agent',
    ];

    /**
     * @param  Builder<UtmVisit>  $query
     * @return Builder<UtmVisit>
     */
    public function scopeForSource(Builder $query, string $source): Builder
    {
        return $query->where('utm_source', $source);
    }
}
