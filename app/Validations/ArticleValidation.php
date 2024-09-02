<?php

namespace App\Validations;

use Illuminate\Support\Facades\Validator;

class ArticleValidation
{
    public static function validate(array $data)
    {
        return Validator::make($data, self::rules(), self::messages());
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity_in_stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'article est obligatoire.',
            'name.string' => 'Le nom de l\'article doit être une chaîne de caractères.',
            'name.max' => 'Le nom de l\'article ne peut pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'quantity_in_stock.required' => 'La quantité en stock est obligatoire.',
            'quantity_in_stock.integer' => 'La quantité en stock doit être un nombre entier.',
            'quantity_in_stock.min' => 'La quantité en stock ne peut pas être négative.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix ne peut pas être négatif.',
        ];
    }
}
