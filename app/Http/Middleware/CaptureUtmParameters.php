<?php

namespace App\Http\Middleware;

use App\Services\Marketing\UtmAttribution;
use App\Services\Marketing\UtmVisitRecorder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureUtmParameters
{
    public function __construct(
        private UtmAttribution $utmAttribution,
        private UtmVisitRecorder $utmVisitRecorder,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            $captured = $this->utmAttribution->capture($request);

            if ($captured !== []) {
                $this->utmVisitRecorder->recordLanding($request, $captured);
            }
        }

        return $next($request);
    }
}
