<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

trait HasSlug
{
    /**
     * Translatable attribute used to build each locale's slug (e.g. title, name).
     */
    protected static string $slugFromAttribute = 'name';

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Empty slug would become an empty URL segment; Laravel then treats {record} as missing.
     */
    public function getRouteKey(): mixed
    {
        $key = parent::getRouteKey();

        if ($this->getRouteKeyName() === 'slug' && ($key === null || $key === '')) {
            return $this->getAttribute($this->getKeyName());
        }

        return $key;
    }

    /**
     * Translatable slug is stored as JSON; default binding uses {@see Model::resolveRouteBindingQuery}
     * with a plain column match. Resolve against the current locale segment instead.
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        if ($field === 'slug' && method_exists($this, 'setTranslation')) {
            $locale = app()->getLocale();
            $keyName = (new static)->getKeyName();
            $value = urldecode((string) $value);

            return $query->where(function ($q) use ($value, $locale, $keyName): void {
                $q->where("slug->{$locale}", $value);

                foreach (static::slugLocales() as $lang) {
                    if ($lang === $locale) {
                        continue;
                    }
                    $q->orWhere("slug->{$lang}", $value);
                }

                if (is_numeric($value)) {
                    $q->orWhere($keyName, $value);
                }
            });
        }

        return parent::resolveRouteBindingQuery($query, $value, $field);
    }

    public static function bootHasSlug(): void
    {
        static::creating(function (Model $model): void {
            if (! method_exists($model, 'setTranslation')) {
                return;
            }

            $model->syncSlugTranslationsFromSource();
        });

        static::updating(function (Model $model): void {
            if (! method_exists($model, 'setTranslation')) {
                return;
            }

            if ($model->isDirty($model->slugFromAttribute())) {
                $model->syncSlugTranslationsFromSource();
            }
        });
    }

    protected function slugFromAttribute(): string
    {
        return static::$slugFromAttribute;
    }

    /**
     * @return array<int, string>
     */
    protected static function slugLocales(): array
    {
        return array_keys(LaravelLocalization::getSupportedLocales());
    }

    protected function syncSlugTranslationsFromSource(): void
    {
        $source = $this->slugFromAttribute();

        foreach (static::slugLocales() as $locale) {
            $raw = $this->getTranslation($source, $locale) ?? '';
            $this->setTranslation(
                'slug',
                $locale,
                static::generateUniqueSlug((string) $raw, $locale, $this->exists ? $this->getKey() : null)
            );
        }
    }

    /**
        Generate a unique slug for the model.
     */
    protected static function generateUniqueSlug(string $value, string $locale, ?int $exceptId = null): string
    {
        if ($locale === 'ar') {
            $slug = preg_replace('/\s+/u', '-', trim($value));
        } else {
            $slug = Str::slug($value);
        }

        if (static::slugExists($slug, $exceptId)) {
            $slug = $slug.'-'.uniqid();
        }

        return $slug;
    }

    /**
     * Translatable slugs are stored as JSON; compare per-locale paths, not the whole column.
     */
    private static function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $query = static::query()->where(function ($q) use ($slug): void {
            foreach (static::slugLocales() as $locale) {
                $q->orWhere("slug->{$locale}", $slug);
            }
        });

        if ($exceptId !== null) {
            $query->where((new static)->getKeyName(), '!=', $exceptId);
        }

        return $query->exists();
    }
}
