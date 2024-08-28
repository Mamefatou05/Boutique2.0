<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $this->client,
            'telephone' => 'required|string|max:15|unique:clients,telephone,' . $this->client,
        ];
    }
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ];
    }
}
