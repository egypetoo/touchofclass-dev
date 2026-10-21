<?php

namespace App\Http\Controllers\Api;

use App\Enums\BranchTypeEnum;
use App\Enums\PageEnum;
use App\Enums\SectionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Http\Resources\ContactResource;
use App\Models\Branch;
use App\Models\ContactMessage;
use App\Models\Section;
use App\Services\Marketing\UtmAttribution;

class ContactController extends Controller
{
    public function index()
    {

        $data['breadcrumb'] = Section::query()
            ->page(PageEnum::CONTACT)
            ->type(SectionTypeEnum::CONTACT_US_BREADCRUMB)
            ->isActive()
            ->first();

        $data['branches'] = Branch::query()
            ->isActive()
            ->get();

        $data['form_content'] = Section::query()
            ->page(PageEnum::CONTACT)
            ->type(SectionTypeEnum::CONTACT_US_FORM)
            ->isActive()
            ->first();
        $data['map_iframe'] = Branch::query()
            ->type(BranchTypeEnum::Main)
            ->isActive()
            ->first()->map_iframe;

        return ContactResource::make($data);
    }

    public function store(StoreContactMessageRequest $request, UtmAttribution $utmAttribution)
    {
        $validated = $request->safe()->only(['service_id', 'project_type', 'name', 'phone', 'email', 'message']);
        $validated = $utmAttribution->mergeInto($validated, $request);

        ContactMessage::create($validated);

        return response()->json([
            'message' => __('site.contact_message_sent'),
        ], 200);
    }
}
