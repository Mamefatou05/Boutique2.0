<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelephoneRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Vérifier que le numéro a exactement 9 chiffres
        if (!preg_match('/^\d{9}$/', $value)) {
            $fail("Le numéro de téléphone doit comporter exactement 9 chiffres.");
            return;
        }

        // Vérifier que le numéro commence par 77, 78, 76, 75, ou 70
        $validPrefixes = ['77', '78', '76', '75', '70'];
        $prefix = substr($value, 0, 2);

        if (!in_array($prefix, $validPrefixes)) {
            $fail("Le numéro de téléphone doit commencer par 77, 78, 76, 75, ou 70.");
        }
    }
}
