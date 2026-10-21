<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PumpSelectorRequest extends FormRequest
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
            'flow' => ['nullable', 'numeric', 'min:0.01'],
            'head' => ['nullable', 'numeric', 'min:0.01'],
            'flow_unit' => ['nullable', 'string', Rule::in(['m3h', 'ls', 'gpm'])],
            'head_unit' => ['nullable', 'string', Rule::in(['m', 'bar', 'ft'])],
        ];
    }
}
