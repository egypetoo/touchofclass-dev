<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sort' => ['nullable', 'in:newest,name,name_asc,name_desc,price_asc,price_desc,power-asc,power-desc'],
            'category' => ['nullable', 'string', 'max:120'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand' => ['nullable', 'array'],
            'brand.*' => ['string', 'max:120'],
            'power_min' => ['nullable', 'numeric', 'min:0'],
            'power_max' => ['nullable', 'numeric', 'min:0'],
            'q' => ['nullable', 'string', 'max:120'],
        ];
    }
}
