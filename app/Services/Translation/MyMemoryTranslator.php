<?php

namespace App\Services\Translation;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MyMemoryTranslator
{
    private const ENDPOINT = 'https://api.mymemory.translated.net/get';

    private const MAX_CHUNK_CHARS = 400;

    /**
     * @param  array<string, mixed>  $fields
     * @return array<string, string>
     */
    public function translateFields(array $fields, string $sourceLocale, string $targetLocale): array
    {
        $translated = [];

        foreach ($fields as $key => $value) {
            if (! is_string($value) || blank(trim(strip_tags($value)))) {
                continue;
            }

            $translated[$key] = $this->translate($value, $sourceLocale, $targetLocale);
        }

        return $translated;
    }

    public function translate(string $text, string $sourceLocale, string $targetLocale): string
    {
        $sourceLocale = $this->normalizeLocale($sourceLocale);
        $targetLocale = $this->normalizeLocale($targetLocale);

        if ($sourceLocale === $targetLocale || blank(trim(strip_tags($text)))) {
            return $text;
        }

        $chunks = $this->chunk($text);
        $parts = [];

        foreach ($chunks as $chunk) {
            $parts[] = $this->requestTranslation($chunk, $sourceLocale, $targetLocale);
        }

        return implode('', $parts);
    }

    protected function requestTranslation(string $text, string $sourceLocale, string $targetLocale): string
    {
        try {
            $response = Http::connectTimeout(3)
                ->timeout(15)
                ->retry([200, 500], 0, function (\Throwable $exception): bool {
                    return $exception instanceof ConnectionException
                        || ($exception instanceof RequestException
                            && ($exception->response?->serverError() || $exception->response?->status() === 429));
                })
                ->get(self::ENDPOINT, [
                    'q' => $text,
                    'langpair' => $sourceLocale.'|'.$targetLocale,
                ]);
        } catch (\Throwable $exception) {
            throw new RuntimeException('Translation service is unavailable. Please try again.', previous: $exception);
        }

        if (! $response->successful()) {
            throw new RuntimeException('Translation service returned an error. Please try again.');
        }

        $translated = $response->json('responseData.translatedText');

        if (! is_string($translated) || $translated === '') {
            throw new RuntimeException('Translation service returned an empty result.');
        }

        // MyMemory returns the original text with a "PLEASE SELECT TWO DISTINCT LANGUAGES" style error sometimes.
        if (Str::contains(Str::lower($translated), ['invalid', 'error', 'quota', 'limit exceeded'])) {
            throw new RuntimeException($translated);
        }

        return html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * @return list<string>
     */
    protected function chunk(string $text): array
    {
        if (mb_strlen($text) <= self::MAX_CHUNK_CHARS) {
            return [$text];
        }

        $chunks = [];
        $buffer = '';

        foreach (preg_split('/(?<=[\.\!\?\n])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [$text] as $sentence) {
            if ($buffer !== '' && mb_strlen($buffer) + mb_strlen($sentence) + 1 > self::MAX_CHUNK_CHARS) {
                $chunks[] = $buffer;
                $buffer = $sentence;
            } else {
                $buffer = $buffer === '' ? $sentence : $buffer.' '.$sentence;
            }
        }

        if ($buffer !== '') {
            $chunks[] = $buffer;
        }

        // Hard-split any remaining oversized piece.
        $normalized = [];
        foreach ($chunks as $chunk) {
            if (mb_strlen($chunk) <= self::MAX_CHUNK_CHARS) {
                $normalized[] = $chunk;

                continue;
            }

            foreach (mb_str_split($chunk, self::MAX_CHUNK_CHARS) as $piece) {
                $normalized[] = $piece;
            }
        }

        return $normalized;
    }

    protected function normalizeLocale(string $locale): string
    {
        return strtolower(str_replace('_', '-', explode('-', $locale)[0]));
    }
}
