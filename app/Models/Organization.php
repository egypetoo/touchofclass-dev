<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model implements HasName
{
    use HasFactory, HasUuids;

    protected static function booted(): void
    {
        static::created(function (Organization $organization): void {
            SiteSetting::firstOrCreate(
                ['organization_id' => $organization->id],
                []
            );
        });
    }

    protected $fillable = [
        'name_en',
        'name_ar',
    ];

    /**
     * Get the Filament name for tenant display.
     */
    public function getFilamentName(): string
    {
        return $this->name_en ?? $this->name_ar ?? 'Organization';
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function siteSetting(): HasOne
    {
        return $this->hasOne(SiteSetting::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function b2bInquiries(): HasMany
    {
        return $this->hasMany(B2bInquiry::class);
    }

    public function blogCategories(): HasMany
    {
        return $this->hasMany(BlogCategory::class);
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function partners(): HasMany
    {
        return $this->hasMany(Partner::class);
    }

    public function newsletterSubscribers(): HasMany
    {
        return $this->hasMany(NewsletterSubscriber::class);
    }
};
