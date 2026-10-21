<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsletterSubscriberRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class NewsletterSubscriberController extends Controller
{
    public function store(StoreNewsletterSubscriberRequest $request): JsonResponse|RedirectResponse
    {
        $email = $request->validated('email');
        $subscriber = NewsletterSubscriber::query()->firstOrCreate(
            ['email' => $email],
        );

        $message = $subscriber->wasRecentlyCreated
            ? __('site.newsletter_subscribed')
            : __('site.newsletter_already_subscribed');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('newsletter_status', $message);
    }
}
