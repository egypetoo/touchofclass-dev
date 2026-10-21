<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use App\Services\RedirectService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RedirectsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = '/'.ltrim($request->getPathInfo(), '/');
        $fullUrl = $request->fullUrl();
        $normalizedPath = rtrim($path, '/');
        $normalizedFullUrl = rtrim($fullUrl, '/');
        $normalizedUrlWithoutQuery = rtrim($request->url(), '/');

        $redirects = Cache::remember('redirects.map', 300, function () {
            return Redirect::query()->where('is_active', true)->get();
        });

        $match = $redirects->first(function (Redirect $redirect) use ($normalizedPath, $normalizedFullUrl, $normalizedUrlWithoutQuery) {
            $normalizedSource = rtrim($redirect->source, '/');
            $sourceWithoutQuery = parse_url($normalizedSource, PHP_URL_PATH);

            if ($sourceWithoutQuery === null) {
                $sourceWithoutQuery = $normalizedSource;
            } else {
                $scheme = parse_url($normalizedSource, PHP_URL_SCHEME);
                $host = parse_url($normalizedSource, PHP_URL_HOST);
                if ($scheme && $host) {
                    $sourceWithoutQuery = $scheme.'://'.$host.$sourceWithoutQuery;
                }
            }

            $sourceWithoutQuery = rtrim((string) $sourceWithoutQuery, '/');

            return $normalizedSource === $normalizedPath
                || $normalizedSource === $normalizedFullUrl
                || $normalizedSource === $normalizedUrlWithoutQuery
                || $sourceWithoutQuery === $normalizedPath
                || $sourceWithoutQuery === $normalizedUrlWithoutQuery;
        });

        if ($match) {
            $status = (int) $match->status_code;

            if ($status === 410) {
                return response()->view('errors.410', [], 410);
            }

            if (! empty($match->target) && RedirectService::isSafeRedirectTarget((string) $match->target)) {
                $target = trim((string) $match->target);
                $statusCode = in_array($status, [301, 302], true) ? $status : 301;

                if (Str::startsWith($target, '/')) {
                    return redirect($target, $statusCode);
                }

                return redirect()->away($target, $statusCode);
            }
        }

        return $next($request);
    }
}
