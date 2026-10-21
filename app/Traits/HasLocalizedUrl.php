<?php

namespace App\Traits;

use App\Support\LocaleUrl;
use Illuminate\Http\RedirectResponse;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

trait HasLocalizedUrl
{
    abstract protected function localizedUrlPath(): string;

    public function getLocalizedUrl(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $slug = $this->getTranslation('slug', $locale);

        if (! filled($slug) && method_exists($this, 'getTranslations')) {
            $all = $this->getTranslations('slug');
            $slug = reset($all) ?: '';
        }

        $slug = trim((string) $slug);
        if ($slug === '') {
            return '';
        }

        return LocaleUrl::canonicalize(
            (string) LaravelLocalization::getLocalizedURL($locale, $this->localizedUrlPath().'/'.$slug),
            $locale
        );
    }

    /**
     * Redirect when the URL slug belongs to another locale.
     */
    public function redirectIfSlugLocaleMismatch(): ?RedirectResponse
    {
        $locale = app()->getLocale();
        $canonicalSlug = trim((string) $this->getTranslation('slug', $locale));
        if ($canonicalSlug === '') {
            return null;
        }

        $pathSlug = urldecode((string) last(request()->segments()));
        if ($pathSlug === $canonicalSlug) {
            return null;
        }

        $url = $this->getLocalizedUrl($locale);

        return filled($url) ? redirect()->to($url, 301) : null;
    }
}
