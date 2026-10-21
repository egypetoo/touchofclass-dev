<?php

namespace App\Models;

use App\Enums\SocialLinkPlatform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSocialLink extends Model
{
    protected $fillable = [
        'site_setting_id',
        'platform',
        'url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function siteSetting(): BelongsTo
    {
        return $this->belongsTo(SiteSetting::class);
    }

    public function platformEnum(): ?SocialLinkPlatform
    {
        return SocialLinkPlatform::tryFrom((string) $this->platform);
    }
}
