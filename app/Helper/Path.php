<?php

namespace App\Helper;

class Path
{
    /**
     * @var list<string>
     */
    private static array $legacyClinicMediaMarkers = [
        'product-card-1.jpg',
        'product-card-2.jpg',
        'product-card-4.jpg',
        'product-card-5.jpg',
        'packaging-boxes',
        'hero-mobile',
        'hero-doctor',
        'doctor-about',
        'doctor-philosophy',
        'philosophy-doctor',
        'gold-tooth',
        'service-1.',
        'service-2.',
        'service-3.',
        'service-4.',
        'service-5.',
        '/3.webp',
        '/2.webp',
        '/1.webp',
        'after-before',
        'after&before',
    ];

    public static function publicPath($path = '')
    {
        return public_path($path);
    }

    public static function cssPath($path = '')
    {
        return self::versionedAsset('assets/css/'.$path);
    }

    public static function jsPath($path = '')
    {
        return self::versionedAsset('assets/js/'.$path);
    }

    /**
     * Append the file's modification time so browsers fetch the new copy after every edit.
     */
    protected static function versionedAsset(string $relativePath): string
    {
        $url = asset($relativePath);
        $absolutePath = public_path($relativePath);

        if (! is_file($absolutePath)) {
            return $url;
        }

        return $url.'?v='.filemtime($absolutePath);
    }

    public static function vendorPath($path = '')
    {
        return asset('assets/vendors/'.$path);
    }

    public static function imagesPath($path = '')
    {
        return asset('assets/images/'.$path);
    }

    /**
     * Use uploaded media when available; otherwise fall back to a King Motors design image.
     * Legacy clinic and Mina Pack media filenames are treated as missing.
     * Local /storage media that is not publicly reachable also falls back.
     */
    public static function mediaOrDesign(?string $mediaUrl, string $designImagePath, bool $allowOversized = false): string
    {
        $fallback = self::imagesPath(ltrim($designImagePath, '/'));

        if (
            ! filled($mediaUrl)
            || self::isLegacyClinicMedia($mediaUrl)
            || self::isUnreachableLocalStorageMedia($mediaUrl)
            || (! $allowOversized && self::isOversizedLocalStorageMedia($mediaUrl))
        ) {
            return $fallback;
        }

        return $mediaUrl;
    }

    public static function isLegacyClinicMedia(string $url): bool
    {
        $path = strtolower((string) (parse_url($url, PHP_URL_PATH) ?: $url));

        foreach (self::$legacyClinicMediaMarkers as $marker) {
            if (str_contains($path, $marker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * True when the URL points at /storage/... but the file is missing under public/storage.
     */
    public static function isUnreachableLocalStorageMedia(string $url): bool
    {
        $absolute = self::localStorageAbsolutePath($url);

        if ($absolute === null) {
            return false;
        }

        return ! is_file($absolute);
    }

    /**
     * @return list<string>
     */
    public static function serviceDesignImages(): array
    {
        return [
            'services/service-consulting.webp',
            'services/service-spareparts.webp',
            'services/service-maintenance.webp',
            'services/service-supply.webp',
        ];
    }

    /**
     * @return list<string>
     */
    public static function heroDesignImages(): array
    {
        return [
            'hero/hero-1.webp',
            'hero/hero-2.webp',
            'hero/hero-3.webp',
        ];
    }

    /**
     * Mobile-sized hero variants for LCP on small viewports.
     *
     * @return list<string>
     */
    public static function heroDesignMobileImages(): array
    {
        return [
            'hero/hero-800.webp',
            'hero/hero-2-800.webp',
            'hero/hero-3-800.webp',
        ];
    }

    /**
     * @return list<string>
     */
    public static function categoryDesignImages(): array
    {
        return [
            'products/motor-ie3.webp',
            'products/pump-centrifugal.webp',
            'products/pipe-hdpe.webp',
            'products/cable-power.webp',
        ];
    }

    /**
     * True when a local /storage media file is large enough to destroy mobile LCP.
     */
    public static function isOversizedLocalStorageMedia(string $url, int $maxBytes = 350_000): bool
    {
        $absolute = self::localStorageAbsolutePath($url);

        if ($absolute === null) {
            return false;
        }

        return is_file($absolute) && filesize($absolute) > $maxBytes;
    }

    /**
     * Resolve a /storage/... URL to an absolute path under public/storage.
     * Decodes URL-encoded segments (Arabic filenames, spaces, etc.).
     */
    public static function localStorageAbsolutePath(string $url): ?string
    {
        $path = (string) (parse_url($url, PHP_URL_PATH) ?: '');

        if ($path === '' || ! str_starts_with($path, '/storage/')) {
            return null;
        }

        $relative = ltrim(substr($path, strlen('/storage/')), '/');

        if ($relative === '') {
            return null;
        }

        $relative = rawurldecode($relative);

        return public_path('storage/'.$relative);
    }

    public static function mimeTypeForAssetUrl(string $url): string
    {
        $path = strtolower((string) (parse_url($url, PHP_URL_PATH) ?: $url));

        return match (true) {
            str_ends_with($path, '.webp') => 'image/webp',
            str_ends_with($path, '.png') => 'image/png',
            str_ends_with($path, '.gif') => 'image/gif',
            str_ends_with($path, '.svg') => 'image/svg+xml',
            default => 'image/jpeg',
        };
    }

    /**
     * @return list<string>
     */
    public static function projectDesignImages(): array
    {
        return [
            'services/service-supply.webp',
            'services/service-maintenance.webp',
            'services/service-consulting.webp',
            'products/products-banner.webp',
            'about/about.webp',
        ];
    }
}
