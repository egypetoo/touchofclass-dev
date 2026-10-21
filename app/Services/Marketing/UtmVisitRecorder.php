<?php

namespace App\Services\Marketing;

use App\Models\UtmVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UtmVisitRecorder
{
    public const SESSION_FINGERPRINT_KEY = 'utm_visit_fingerprint';

    public function __construct(
        private UtmAttribution $utmAttribution,
    ) {}

    /**
     * Persist a visit when the request carries a new UTM touch for this session.
     *
     * @param  array<string, string>  $captured
     */
    public function recordLanding(Request $request, array $captured): void
    {
        if ($captured === [] || blank($captured['utm_source'] ?? null)) {
            return;
        }

        if ($this->shouldSkip($request)) {
            return;
        }

        $fingerprint = $this->fingerprint($captured);

        if ($request->hasSession() && $request->session()->get(self::SESSION_FINGERPRINT_KEY) === $fingerprint) {
            return;
        }

        UtmVisit::query()->create([
            'utm_source' => $captured['utm_source'] ?? null,
            'utm_medium' => $captured['utm_medium'] ?? null,
            'utm_campaign' => $captured['utm_campaign'] ?? null,
            'utm_term' => $captured['utm_term'] ?? null,
            'utm_content' => $captured['utm_content'] ?? null,
            'path' => Str::limit('/'.ltrim($request->path(), '/'), 191, ''),
            'landing_page' => Str::limit($request->fullUrl(), 500, ''),
            'referrer' => $this->utmAttribution->sanitize($request->headers->get('referer')),
            'session_id' => $request->hasSession() ? Str::limit((string) $request->session()->getId(), 100, '') : null,
            'ip_hash' => hash('sha256', (string) $request->ip().'|'.(string) config('app.key')),
            'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
        ]);

        if ($request->hasSession()) {
            $request->session()->put(self::SESSION_FINGERPRINT_KEY, $fingerprint);
        }
    }

    /**
     * @param  array<string, string>  $captured
     */
    protected function fingerprint(array $captured): string
    {
        return hash('sha256', implode('|', [
            $captured['utm_source'] ?? '',
            $captured['utm_medium'] ?? '',
            $captured['utm_campaign'] ?? '',
            $captured['utm_term'] ?? '',
            $captured['utm_content'] ?? '',
        ]));
    }

    protected function shouldSkip(Request $request): bool
    {
        return $request->is(
            'admin',
            'admin/*',
            'livewire',
            'livewire/*',
            'api',
            'api/*',
            'up',
            'robots.txt',
            'manifest.json',
            'sitemap.xml',
        );
    }
}
