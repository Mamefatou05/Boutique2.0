<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PasswordRule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => ['required','string','max:255', 'unique:users'],
            'password' => ['required', new PasswordRule,'confirmed'],
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',  // Vérifie si le champ photo est une image et ne dépasse pas 2MB
            'clientid' => 'required|exists:clients,id'  // Vérifie si le clientid existe dans la table clients
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prenom est obligatoire.',
            'login.unique' => 'Cet login est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'photo.image' => 'Le champ photo doit être une image.',
            'photo.mimes' => 'Le champ photo doit être au format jpeg, png, jpg ou gif.',
            'photo.max' => 'Le champ photo ne doit pas dépasser 2MB.',
            'clientid.required' => 'Le clientid est obligatoire.',
            'clientid.exists' => 'Le clientid n\'existe pas dans la table clients'
        ];
    
    }
}

