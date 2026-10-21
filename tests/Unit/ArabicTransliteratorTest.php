<?php

use App\Support\ArabicTransliterator;

it('transliterates arabic product names for english labels', function () {
    expect(ArabicTransliterator::containsArabic('جلدة ارضية'))->toBeTrue()
        ->and(ArabicTransliterator::containsArabic('Floor Mat'))->toBeFalse()
        ->and(ArabicTransliterator::toLatin('جلدة ارضية'))->not->toMatch('/\p{Arabic}/u')
        ->and(ArabicTransliterator::toLatin('جلدة ارضية'))->not->toBe('')
        ->and(ArabicTransliterator::toLatin('V60'))->toBe('V60');
});
