<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LayoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $siteSetting = $this['siteSetting'];

        return [
            'branding' => $siteSetting ? [
                'site_name' => $siteSetting->siteName(),
                'logo' => $siteSetting->logoUrl(),
                'favicon' => $siteSetting->faviconUrl(),
            ] : null,
            'menu' => MenuResource::collection($this['mainMenu']),
            'footer' => [
                'body' => $siteSetting->footerBody(),
                'copyright' => $siteSetting->{'footer_copyright_'.app()->getLocale()},
                'links' => MenuResource::collection($this['footerNavigation']),
            ],
            'social_links' => SocialLinkResource::collection($siteSetting->socialLinks),
            'call_to_actions' => [
                'phones' => $siteSetting->ctaPhoneEntries(),
                'whatsapp' => $siteSetting->ctaWhatsappEntries(),
                'phone' => $siteSetting->primaryCtaPhone(),
                'whatsapp_primary' => $siteSetting->primaryCtaWhatsapp(),
            ],
            'contact' => array_merge(
                (new BranchResource($this['mainBranch']))->toArray($request),
                ['support_email' => $siteSetting->support_email]
            ),
        ];
    }
}
