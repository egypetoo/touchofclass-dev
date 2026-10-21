<?php

namespace App\Http\Controllers;

use App\Models\PerformanceMetric;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class PerformanceMetricController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $key = 'performance-metrics:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return response()->json(['success' => false], 429);
        }
        RateLimiter::hit($key, 60);

        $data = $request->validate([
            'metric' => 'required|string|in:CLS,FID,LCP,FCP,TTFB,INP',
            'value' => 'required|numeric',
            'rating' => 'required|string|in:good,needs-improvement,poor',
            'page' => 'required|string|max:2048',
        ]);

        PerformanceMetric::query()->create([
            ...$data,
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['success' => true]);
    }
}
