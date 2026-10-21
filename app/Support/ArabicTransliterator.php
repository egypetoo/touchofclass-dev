<?php

namespace App\Support;

class ArabicTransliterator
{
    /**
     * Approximate Arabic → Latin for English catalog labels when no EN translation exists.
     */
    public static function toLatin(string $text): string
    {
        $text = trim($text);

        if ($text === '' || ! preg_match('/\p{Arabic}/u', $text)) {
            return $text;
        }

        $map = [
            'أ' => 'a', 'إ' => 'i', 'آ' => 'a', 'ا' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th',
            'ج' => 'j', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd', 'ذ' => 'dh', 'ر' => 'r', 'ز' => 'z',
            'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd', 'ط' => 't', 'ظ' => 'z', 'ع' => 'a',
            'غ' => 'gh', 'ف' => 'f', 'ق' => 'q', 'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n',
            'ه' => 'h', 'و' => 'w', 'ي' => 'y', 'ى' => 'a', 'ة' => 'a', 'ء' => '', 'ؤ' => 'o',
            'ئ' => 'e', 'لا' => 'la', 'ً' => '', 'ٌ' => '', 'ٍ' => '', 'َ' => '', 'ُ' => '',
            'ِ' => '', 'ّ' => '', 'ْ' => '', 'ـ' => '',
        ];

        $latin = strtr($text, $map);
        $latin = preg_replace('/[^\p{L}\p{N}\s\-_\/&+.]/u', ' ', $latin) ?? $latin;
        $latin = preg_replace('/\s+/u', ' ', $latin) ?? $latin;
        $latin = trim($latin);

        if ($latin === '') {
            return $text;
        }

        return mb_convert_case($latin, MB_CASE_TITLE, 'UTF-8');
    }

    public static function containsArabic(string $text): bool
    {
        return (bool) preg_match('/\p{Arabic}/u', $text);
    }
}
