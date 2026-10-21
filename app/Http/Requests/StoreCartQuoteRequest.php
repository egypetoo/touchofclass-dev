<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCartQuoteRequest extends FormRequest
{
    /**
     * @var array<string, string>
     */
    public const CITIES = [
        'cairo' => 'القاهرة',
        'giza' => 'الجيزة',
        'alex' => 'الإسكندرية',
        'qalyubia' => 'القليوبية',
        'sharqia' => 'الشرقية',
        'other' => 'محافظة أخرى',
    ];

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
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['required', 'string', Rule::in(array_keys(self::CITIES))],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['nullable', 'array'],
            'items.*.qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ];
    }
}
