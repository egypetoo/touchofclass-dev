<?php

namespace App\Http\Controllers\Site;

use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\Section;
use App\Models\Service;
use App\Services\Mail\FormNotificationService;
use App\Services\Marketing\UtmAttribution;
use App\Services\SiteLayoutDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactUsController extends Controller
{
    public function index(Request $request, SiteLayoutDataService $layoutData): View
    {
        $siteSetting = $layoutData->siteSetting();
        $mainBranch = $layoutData->mainBranch();

        $sections = Section::query()
            ->with('media')
            ->page(PageEnum::CONTACT)
            ->IsActive()
            ->orderBy('order')
            ->get()
            ->groupBy(fn (Section $section): string => (string) $section->type);

        return view('site.contact-us.index', [
            'breadcrumb' => $sections->get(SectionTypeEnum::CONTACT_US_BREADCRUMB->value)?->first(),
            'contact_form_section' => $sections->get(SectionTypeEnum::CONTACT_US_FORM->value)?->first(),
            'contact_content' => $sections->get(SectionTypeEnum::CONTACT_US_CONTENT->value)?->first(),
            'contactBadges' => $sections->get(SectionTypeEnum::BENEFITS->value) ?? collect(),
            'contactChannelsIntro' => $sections->get(SectionTypeEnum::CONTENT->value)?->first(),
            'contactChannelCards' => ($sections->get(SectionTypeEnum::FEATURES->value) ?? collect())->keyBy('order'),
            'contactWorkingHours' => $mainBranch?->working_hours,
            'turnstileEnabled' => $siteSetting?->isTurnstileEnabled() ?? false,
            'turnstileSiteKey' => $siteSetting?->turnstile_site_key,
            'contactServices' => Service::query()->roots()->isActive()->orderBy('order')->get(),
            'contactMapSrc' => 'https://www.google.com/maps?q='.urlencode(
                $mainBranch?->address ?: 'Obour City Industrial Zone, Egypt'
            ).'&output=embed',
            'mapTitle' => $siteSetting?->siteName() ?: config('app.name'),
        ]);
    }

    public function store(
        StoreContactMessageRequest $request,
        FormNotificationService $mailer,
        UtmAttribution $utmAttribution,
    ): JsonResponse|RedirectResponse {
        $validated = $request->safe()->only(['service_id', 'project_type', 'name', 'phone', 'email', 'message']);

        $messageParts = [];
        if ($request->filled('company')) {
            $messageParts[] = __('site.company').': '.$request->input('company');
        }
        if ($request->filled('appointment_date')) {
            $messageParts[] = __('site.appointment_date').': '.$request->input('appointment_date');
        }
        if ($request->filled('appointment_time')) {
            $messageParts[] = __('site.appointment_time').': '.$request->input('appointment_time');
        }
        if (filled($validated['message'] ?? null)) {
            $messageParts[] = $validated['message'];
        }

        $validated['message'] = $messageParts !== []
            ? implode("\n", $messageParts)
            : __('site.consultation_request');

        $validated = $utmAttribution->mergeInto($validated, $request);

        $message = ContactMessage::create($validated);
        $message->load('service');

        $requestType = $message->service?->getTranslation('title', 'en') ?: $message->project_type;

        $notification = [
            'Request type' => $requestType,
            'Name' => $message->name,
            'Phone' => $message->phone,
            'Email' => $message->email,
            'Message' => $message->message,
        ];

        if (filled($message->utm_source)) {
            $notification['Source'] = $utmAttribution->platformLabel($message->utm_source)
                ?? $message->utm_source;
        }

        if (filled($message->utm_campaign)) {
            $notification['Campaign'] = $message->utm_campaign;
        }

        $mailer->sendContactNotification($notification);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('site.contact_message_sent'),
            ]);
        }

        return redirect()->route('contact.thanks');
    }

    public function thanks(): View
    {
        return view('site.contact-us.thanks');
    }
}
