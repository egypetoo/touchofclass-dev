<?php

use App\Helper\Path;

it('falls back to the design image when media is missing', function () {
    $resolved = Path::mediaOrDesign(null, 'hero/hero-1.webp');

    expect($resolved)->toBe(Path::imagesPath('hero/hero-1.webp'));
});

it('falls back when local storage media is not publicly reachable', function () {
    $resolved = Path::mediaOrDesign(
        'https://example.test/storage/media/sections/999/missing-hero.jpg',
        'hero/hero-2.webp'
    );

    expect($resolved)->toBe(Path::imagesPath('hero/hero-2.webp'));
});

it('keeps a reachable local storage media url when the file is reasonably sized', function () {
    $relative = 'media/path-ok-hero-test.jpg';
    $publicFile = public_path('storage/'.$relative);
    $dir = dirname($publicFile);

    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($publicFile, str_repeat('1', 1024));

    try {
        $mediaUrl = asset('storage/'.$relative);
        $resolved = Path::mediaOrDesign($mediaUrl, 'hero/hero-3.webp');

        expect($resolved)->toBe($mediaUrl);
    } finally {
        @unlink($publicFile);
    }
});

it('falls back when local storage media is oversized', function () {
    $relative = 'media/path-oversized-hero-test.bin';
    $publicFile = public_path('storage/'.$relative);
    $dir = dirname($publicFile);

    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($publicFile, str_repeat('0', 400_001));

    try {
        $mediaUrl = asset('storage/'.$relative);
        $resolved = Path::mediaOrDesign($mediaUrl, 'hero/hero-1.webp');

        expect($resolved)->toBe(Path::imagesPath('hero/hero-1.webp'));
    } finally {
        @unlink($publicFile);
    }
});

it('keeps reachable local storage media with url-encoded arabic filenames', function () {
    $relative = 'media/path-arabic-test/صوص-منت.jpg';
    $publicFile = public_path('storage/'.$relative);
    $dir = dirname($publicFile);

    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($publicFile, str_repeat('1', 2048));

    try {
        $encodedRelative = 'media/path-arabic-test/'.rawurlencode('صوص-منت.jpg');
        $mediaUrl = asset('storage/'.$encodedRelative);
        $resolved = Path::mediaOrDesign($mediaUrl, 'products/product-1.png', allowOversized: true);

        expect($resolved)->toBe($mediaUrl)
            ->and(Path::isUnreachableLocalStorageMedia($mediaUrl))->toBeFalse();
    } finally {
        @unlink($publicFile);
        @rmdir(dirname($publicFile));
    }
});

it('keeps oversized media when allowOversized is true', function () {
    $relative = 'media/path-oversized-allowed-test.bin';
    $publicFile = public_path('storage/'.$relative);
    $dir = dirname($publicFile);

    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($publicFile, str_repeat('0', 400_001));

    try {
        $mediaUrl = asset('storage/'.$relative);
        $resolved = Path::mediaOrDesign($mediaUrl, 'products/product-1.png', allowOversized: true);

        expect($resolved)->toBe($mediaUrl);
    } finally {
        @unlink($publicFile);
    }
});

it('treats legacy clinic media as missing', function () {
    $resolved = Path::mediaOrDesign(
        'https://example.test/storage/media/sections/1/hero-desktop.webp',
        'hero/hero-1.webp'
    );

    expect($resolved)->toBe(Path::imagesPath('hero/hero-1.webp'));
});

it('resolves mime types for preload hints', function () {
    expect(Path::mimeTypeForAssetUrl('https://example.test/assets/images/hero/hero-1.webp'))
        ->toBe('image/webp')
        ->and(Path::mimeTypeForAssetUrl('/assets/images/hero/hero-1.jpg'))
        ->toBe('image/jpeg');
});

it('exposes King Motors hero design paths', function () {
    expect(Path::heroDesignImages())->toHaveCount(3)
        ->and(Path::heroDesignMobileImages())->toHaveCount(3)
        ->and(Path::heroDesignImages()[0])->toBe('hero/hero-1.webp')
        ->and(Path::heroDesignMobileImages()[0])->toBe('hero/hero-800.webp');
});

it('builds a smaller webp for a wide local photo', function () {
    if (! function_exists('imagewebp')) {
        test()->markTestSkipped('GD webp is unavailable');
    }

    $relative = 'media/path-display-hero-test.jpg';
    $publicFile = public_path('storage/'.$relative);
    $directory = dirname($publicFile);

    if (! is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $image = imagecreatetruecolor(1200, 200);
    imagejpeg($image, $publicFile, 90);
    imagedestroy($image);

    $generated = null;

    try {
        $url = Path::displayImage(asset('storage/'.$relative), 960);
        $path = parse_url($url, PHP_URL_PATH);
        $generated = public_path(ltrim((string) $path, '/'));
        $info = getimagesize($generated);

        expect($url)->toContain('/storage/optimized/')
            ->and($url)->toEndWith('.webp')
            ->and($info)->not->toBeFalse()
            ->and($info[0])->toBeLessThanOrEqual(960);
    } finally {
        @unlink($publicFile);

        if (is_string($generated)) {
            @unlink($generated);
        }
    }
});
