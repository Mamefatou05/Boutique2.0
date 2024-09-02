<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
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
    public function rules()
    {
        return [
            'articles' => 'required|array',
             'articles.*.id' => 'required', // Vérifie si l'ID existe dans la table articles
            'articles.*.qte' => 'required|integer|min:1', // Vérifie si la quantité est valide
        ];
    }
    // |exists:articles,id

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    // public function messages()
    // {
    //     return [
    //         'articles.required' => 'Le champ des articles est requis.',
    //         'articles.array' => 'Le champ des articles doit être un tableau.',
    //         'articles.*.id.required' => 'L\'ID de l\'article est requis.',
    //         // 'articles.*.id.exists' => 'L\'ID de l\'article :input n\'existe pas dans la base de données.',
    //         'articles.*.qte.required' => 'La quantité est requise pour chaque article.',
    //         'articles.*.qte.integer' => 'La quantité doit être un nombre entier.',
    //         'articles.*.qte.min' => 'La quantité doit être au moins :min.',
    //     ];
    // }
}
