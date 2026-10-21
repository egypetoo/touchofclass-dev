<?php

namespace App\Services\Seo;

use App\Models\SiteSetting;
use App\Services\AI\ContentGenerationService;

class SeoAiService
{
    public function __construct(
        protected ContentGenerationService $ai,
    ) {}

    /**
     * @return array{success: bool, data?: array<string, mixed>, error?: string}
     */
    public function generateForPage(string $pageKey): array
    {
        $site = SiteSetting::query()->first();
        $siteName = $site?->siteName() ?? config('app.name');
        $context = "Website: {$siteName}. Page type: {$pageKey}.";

        $result = [
            'success' => true,
            'data' => [],
        ];

        foreach (['en', 'ar'] as $lang) {
            $langLabel = $lang === 'ar' ? 'Arabic' : 'English';

            $titlePrompt = "Write an SEO meta title (max 60 characters) in {$langLabel} for the {$pageKey} page. {$context} Return only the title text.";
            $descPrompt = "Write an SEO meta description (max 160 characters) in {$langLabel} for the {$pageKey} page. {$context} Return only the description text.";
            $keywordsPrompt = "Write 5-8 SEO keywords comma-separated in {$langLabel} for the {$pageKey} page. {$context} Return only keywords.";

            $title = $this->ai->generateText($titlePrompt, [
                'max_tokens' => 120,
                'system_prompt' => 'You are an SEO expert. Return plain text only.',
            ]);
            if (! $title['success']) {
                return ['success' => false, 'error' => $title['error'] ?? 'Title generation failed'];
            }

            $desc = $this->ai->generateText($descPrompt, [
                'max_tokens' => 200,
                'system_prompt' => 'You are an SEO expert. Return plain text only.',
            ]);
            if (! $desc['success']) {
                return ['success' => false, 'error' => $desc['error'] ?? 'Description generation failed'];
            }

            $keywords = $this->ai->generateText($keywordsPrompt, [
                'max_tokens' => 120,
                'system_prompt' => 'You are an SEO expert. Return plain text only.',
            ]);

            $result['data']["{$pageKey}_meta_title_{$lang}"] = mb_substr($title['content'], 0, 255);
            $result['data']["{$pageKey}_meta_desc_{$lang}"] = $desc['content'];
            $result['data']["{$pageKey}_meta_keywords_{$lang}"] = ($keywords['success'] ?? false)
                ? mb_substr($keywords['content'], 0, 255)
                : '';
            $result['data']["{$pageKey}_meta_robots_{$lang}"] = 'index, follow';
        }

        return $result;
    }
}
