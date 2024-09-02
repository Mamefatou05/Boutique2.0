<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Rules\PasswordRule;
use App\Rules\TelephoneRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'surname' => ['required', 'string', 'max:255', 'unique:clients,surname'],
            'address' => ['nullable', 'string', 'max:255'],
            'telephone' => ['required', new TelephoneRule()],
            'user' => ['sometimes', 'array'],
            'user.nom' => ['required_with:user', 'string'],
            'user.prenom' => ['required_with:user', 'string'],
            'user.login' => ['required_with:user', 'string', 'unique:users,login'],
            // 'user.role' => ['required_with:user', 'in:' . implode(',', array_column(Role::cases(), 'value'))],
            'user.password' => ['required_with:user', new PasswordRule(), 'confirmed'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'surname.required' => 'Le surnom est obligatoire.',
            'user.login.unique' => 'Le login est déjà utilisé.',
            'user.password.confirmed' => 'Les mots de passe ne correspondent pas.',
            // 'user.role.in' => 'Le rôle doit être un des choix suivants : :values.',
            // 'user.role.required_with' => 'Le rôle est obligatoire si le champ utilisateur est fourni.',
            'user.nom.required_with' => 'Le nom est obligatoire si le champ utilisateur est fourni.',
            'user.prenom.required_with' => 'Le prénom est obligatoire si le champ utilisateur est fourni.',
            'user.password.required_with' => 'Le mot de passe est obligatoire si le champ utilisateur est fourni.',
            'address.string' => 'L\'adresse doit être une chaîne de caractères.',
            'address.max' => 'L\'adresse ne doit pas dépasser 255 caractères.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone.unique' => 'Le numéro de téléphone est déjà utilisé.',
            'surname.string' => 'Le surnom doit être une chaîne de caractères.',
            'surname.max' => 'Le surnom ne doit pas dépasser 255 caractères.',
            'surname.unique' => 'Le surnom est déjà utilisé.',
            'user.nom.string' => 'Le nom doit être une chaîne de caractères.',
            'user.prenom.string' => 'Le prénom doit être une chaîne de caractères.'

        
        ];
    }
}
 