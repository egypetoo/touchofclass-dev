<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'address' => $this->address,
            'map_url' => $this->map_url,
            'map_embed' => $this->map_embed,
            'phone_1' => $this->phone_1,
            'phone_1_country_code' => $this->phone_1_country_code,
            'phone_2' => $this->phone_2,
            'phone_2_country_code' => $this->phone_2_country_code,
            'working_hours' => $this->working_hours,
            'email' => $this->email,
        ];
    }
}
