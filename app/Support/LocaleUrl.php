<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class LocaleUrl
{
    /**
     * @var list<string>
     */
    protected static array $localizedRouteParams = [
        'service',
        'blog',
        'product',
        'project',
        'category',
    ];

    /**
     * Query parameters that produce distinct, indexable listing pages, keyed by path without locale.
     *
     * @var array<string, list<string>>
     */
    protected static array $indexableQueryParams = [
        '/products' => ['category_id', 'page'],
    ];

    /**
     * Build the correct URL for switching to another locale,
     * using the model's translated slug on detail pages.
     */
    public static function forLocale(string $locale): string
    {
        $model = static::currentLocalizedModel();

        if ($model && method_exists($model, 'getLocalizedUrl')) {
            $url = $model->getLocalizedUrl($locale);
            if (filled($url)) {
                return static::canonicalize($url, $locale);
            }
        }

        return static::toLocalePath($locale, static::currentPathWithoutLocale()).static::indexableQueryString();
    }

    /**
     * Query string of the current request reduced to the parameters that make the listing indexable.
     */
    public static function indexableQueryString(): string
    {
        $allowedParams = static::$indexableQueryParams[static::currentPathWithoutLocale()] ?? [];

        $query = collect(request()->only($allowedParams))
            ->filter(fn (mixed $value, string $key): bool => is_string($value)
                && ctype_digit($value)
                && (int) $value > ($key === 'page' ? 1 : 0))
            ->sortKeys()
            ->all();

        return $query === [] ? '' : '?'.http_build_query($query);
    }

    /**
     * Absolute URL for a path in the given locale (path without locale prefix).
     */
    public static function toLocalePath(string $locale, string $path = '/'): string
    {
        $path = '/'.ltrim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        $defaultLocale = (string) config('app.locale');
        $hideDefault = (bool) config('laravellocalization.hideDefaultLocaleInURL');

        if ($hideDefault && $locale === $defaultLocale) {
            return url($path === '/' ? '/' : $path);
        }

        if ($path === '/') {
            return url('/'.$locale);
        }

        return url('/'.$locale.$path);
    }

    /**
     * When hideDefaultLocaleInURL is enabled, strip a redundant default-locale prefix.
     * When disabled (Arabic at `/ar`), URLs are left unchanged.
     */
    public static function canonicalize(string $url, string $locale): string
    {
        if (! config('laravellocalization.hideDefaultLocaleInURL')) {
            return $url;
        }

        $defaultLocale = (string) config('app.locale');
        $parts = parse_url($url);

        if ($parts === false) {
            return $url;
        }

        $path = $parts['path'] ?? '/';
        $supported = array_keys(config('laravellocalization.supportedLocales', []));
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        $pathLocale = ($segments[0] ?? null);
        $pathHasLocale = is_string($pathLocale) && in_array($pathLocale, $supported, true);
        $pathWithoutLocale = $pathHasLocale
            ? '/'.implode('/', array_slice($segments, 1))
            : ($path === '' ? '/' : $path);

        if ($pathWithoutLocale === '/') {
            $pathWithoutLocale = '/';
        }

        // Target locale should hide default prefix, or keep non-default prefix.
        if ($locale === $defaultLocale) {
            $canonicalPath = $pathWithoutLocale === '/' ? '/' : $pathWithoutLocale;
        } else {
            $canonicalPath = $pathWithoutLocale === '/'
                ? '/'.$locale
                : '/'.$locale.$pathWithoutLocale;
        }

        $rebuilt = '';
        if (isset($parts['scheme'], $parts['host'])) {
            $rebuilt = $parts['scheme'].'://'.$parts['host'];
            if (isset($parts['port'])) {
                $rebuilt .= ':'.$parts['port'];
            }
            $rebuilt .= ($canonicalPath === '/' ? '/' : $canonicalPath);
        } else {
            $rebuilt = url($canonicalPath);
        }

        if (! empty($parts['query'])) {
            $rebuilt .= '?'.$parts['query'];
        }

        return $rebuilt;
    }

    public static function currentPathWithoutLocale(): string
    {
        $segments = request()->segments();
        $supported = array_keys(config('laravellocalization.supportedLocales', []));

        if ($segments !== [] && in_array($segments[0], $supported, true)) {
            array_shift($segments);
        }

        return $segments === [] ? '/' : '/'.implode('/', $segments);
    }

    public static function currentLocalizedModel(): ?Model
    {
        $route = request()->route();
        if ($route === null) {
            return null;
        }

        foreach (static::$localizedRouteParams as $param) {
            $value = $route->parameter($param);
            if ($value instanceof Model && method_exists($value, 'getLocalizedUrl')) {
                return $value;
            }
        }

        return null;
    }
}
