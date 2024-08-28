<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Vérifie si le mot de passe contient au moins une lettre majuscule
        if (!preg_match('/[A-Z]/', $value)) {
            $fail('Le :attribute doit contenir au moins une lettre majuscule.');
        }

        // Vérifie si le mot de passe contient au moins une lettre minuscule
        if (!preg_match('/[a-z]/', $value)) {
            $fail('Le :attribute doit contenir au moins une lettre minuscule.');
        }

        // Vérifie si le mot de passe contient au moins un chiffre
        if (!preg_match('/\d/', $value)) {
            $fail('Le :attribute doit contenir au moins un chiffre.');
        }

        // Vérifie si le mot de passe a une longueur minimale de 8 caractères
        if (strlen($value) < 8) {
            $fail('Le :attribute doit contenir au moins 8 caractères.');
        }
    }
}
