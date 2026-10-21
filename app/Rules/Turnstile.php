<?php

namespace App\Rules;

use App\Services\Security\TurnstileVerifier;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Turnstile implements ValidationRule
{
    public function __construct(
        private TurnstileVerifier $verifier,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->verifier->isEnabled()) {
            return;
        }

        if (! $this->verifier->verify(is_string($value) ? $value : null, request()->ip())) {
            $fail(__('site.turnstile_failed'));
        }
    }
}
