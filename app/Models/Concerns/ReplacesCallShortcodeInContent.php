<?php

namespace App\Models\Concerns;

use App\Services\SiteLayoutDataService;

trait ReplacesCallShortcodeInContent
{
    /**
     * Replace #call# (optionally wrapped in empty <p> tags) with phone/WhatsApp buttons.
     */
    protected function replaceCallShortcode(mixed $content): mixed
    {
        if (! is_string($content) || ! str_contains($content, '#call#') || $this->shouldSkipCallShortcodeReplacement()) {
            return $content;
        }

        $callButtonsHtml = view('components.site.content-call-to-actions', [
            'siteSetting' => app(SiteLayoutDataService::class)->siteSetting(),
        ])->render();

        if (blank(trim(strip_tags($callButtonsHtml)))) {
            return (string) preg_replace(
                '/(?:<p>(?:\s|&nbsp;|<br\s*\/?>)*)?#call#(?:(?:\s|&nbsp;|<br\s*\/?>)*<\/p>)?/iu',
                '',
                $content
            );
        }

        return (string) preg_replace(
            '/(?:<p>(?:\s|&nbsp;|<br\s*\/?>)*)?#call#(?:(?:\s|&nbsp;|<br\s*\/?>)*<\/p>)?/iu',
            $callButtonsHtml,
            $content
        );
    }

    protected function shouldSkipCallShortcodeReplacement(): bool
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return false;
        }

        $request = request();

        return $request->is('admin*')
            || $request->is('livewire*')
            || str_starts_with($request->path(), 'admin');
    }
}
