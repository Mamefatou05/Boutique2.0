<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Assurez-vous que vous vérifiez les autorisations adéquates pour votre cas d'utilisation.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'quantity_in_stock' => 'sometimes|nullable|integer|min:0',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'article est obligatoire lorsque fourni.',
            'name.string' => 'Le nom de l\'article doit être une chaîne de caractères.',
            'name.max' => 'Le nom de l\'article ne peut pas dépasser :max caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'quantity_in_stock.integer' => 'La quantité en stock doit être un nombre entier.',
            'quantity_in_stock.min' => 'La quantité en stock ne peut pas être négative.',
        ];
    }
}
