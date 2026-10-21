<?php

namespace App\Http\Resources;

use App\Services\SiteLayoutDataService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'breadcrumb' => SectionResource::make($this['breadcrumb']),
            'branches' => BranchResource::collection($this['branches']),
            'form_content' => SectionResource::make($this['form_content']),
            'map_iframe' => (new SiteLayoutDataService)->mainBranch()->map_embed,

        ];
    }
}
