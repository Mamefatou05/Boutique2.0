<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
            'name' => 'required|string|unique:articles|max:255',
            'description' => 'nullable|string',
            'quantity_in_stock' => 'required|integer|min:0',
            'price' => 'numeric|min:0',
        ];
    }

    /**
     * Obtenir les messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'article est obligatoire.',
            'name.string' => 'Le nom de l\'article doit être une chaîne de caractères.',
            'name.max' => 'Le nom de l\'article ne peut pas dépasser :max caractères.',
            'quantity_in_stock.required' => 'La quantité en stock est obligatoire.',
            'quantity_in_stock.integer' => 'La quantité en stock doit être un nombre entier.',
            'quantity_in_stock.min' => 'La quantité en stock ne peut pas être négative.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix ne peut pas être négatif.',
        ];
    }
}
