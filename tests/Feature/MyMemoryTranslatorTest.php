<?php

use App\Services\Translation\MyMemoryTranslator;
use Illuminate\Support\Facades\Http;

it('translates plain text from english to arabic via mymemory', function () {
    Http::fake([
        'api.mymemory.translated.net/*' => Http::response([
            'responseData' => [
                'translatedText' => 'زراعة الأسنان',
            ],
        ]),
    ]);

    $result = app(MyMemoryTranslator::class)->translate('Dental implants', 'en', 'ar');

    expect($result)->toBe('زراعة الأسنان');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'q=Dental%20implants')
            && (str_contains($request->url(), 'langpair=en%7Car') || str_contains($request->url(), 'langpair=en|ar'));
    });
});

it('translates multiple fields and skips empty values', function () {
    Http::fake([
        'api.mymemory.translated.net/*' => Http::sequence()
            ->push(['responseData' => ['translatedText' => 'ابتسامة هوليوود']])
            ->push(['responseData' => ['translatedText' => 'وصف طويل']]),
    ]);

    $result = app(MyMemoryTranslator::class)->translateFields([
        'title' => 'Hollywood smile',
        'text' => 'Long description',
        'empty' => '',
        'nullish' => null,
    ], 'en', 'ar');

    expect($result)->toBe([
        'title' => 'ابتسامة هوليوود',
        'text' => 'وصف طويل',
    ]);
});

it('chunks long text before translating', function () {
    Http::fake([
        'api.mymemory.translated.net/*' => Http::sequence()
            ->push(['responseData' => ['translatedText' => 'جزء1']])
            ->push(['responseData' => ['translatedText' => 'جزء2']]),
    ]);

    $long = str_repeat('Dental care is important. ', 30);

    $result = app(MyMemoryTranslator::class)->translate($long, 'en', 'ar');

    expect($result)->toBe('جزء1جزء2');
    Http::assertSentCount(2);
});
