<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordPolicy implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || strlen($value) < 8 || strlen($value) > 12) {
            $fail('Password harus 8–12 karakter.');

            return;
        }

        if (! preg_match('/[A-Z]/', $value)) {
            $fail('Password harus mengandung huruf besar.');

            return;
        }

        if (! preg_match('/[0-9]/', $value)) {
            $fail('Password harus mengandung angka.');

            return;
        }

        if (! preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('Password harus mengandung tanda baca/simbol.');
        }
    }
}
