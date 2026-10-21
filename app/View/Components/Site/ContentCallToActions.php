<?php

namespace App\View\Components\Site;

use App\Models\SiteSetting;
use App\Services\SiteLayoutDataService;
use Illuminate\View\Component;
use Illuminate\View\View;

class ContentCallToActions extends Component
{
    public function __construct(
        public ?SiteSetting $siteSetting = null,
    ) {
        $this->siteSetting ??= app(SiteLayoutDataService::class)->siteSetting();
    }

    public function shouldRender(): bool
    {
        return filled($this->siteSetting?->primaryCtaPhone())
            || filled($this->siteSetting?->whatsappUrl());
    }

    public function render(): View
    {
        return view('components.site.content-call-to-actions');
    }
}
