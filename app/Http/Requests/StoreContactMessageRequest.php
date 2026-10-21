<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
use App\Services\Security\TurnstileVerifier;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $turnstileEnabled = app(TurnstileVerifier::class)->isEnabled();

        return [
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:64'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
            'appointment_date' => ['nullable', 'date'],
            'appointment_time' => ['nullable', 'date_format:H:i'],
            'cf-turnstile-response' => $turnstileEnabled
                ? ['required', 'string', app(Turnstile::class)]
                : ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => __('site.turnstile_required'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->input('name', $this->input('user_name')),
            'email' => $this->input('email', $this->input('user_email')),
            'phone' => $this->input('phone', $this->input('contact_number')),
            'service_id' => $this->input('service_id', $this->input('user_service')),
            'message' => $this->input('message') ?: '',
        ]);
    }
}
