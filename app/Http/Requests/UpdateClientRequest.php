<?php

namespace App\Http\Requests;

use App\Rules\TelephoneRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
 
    
    
        public function authorize()
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
                'telephone' => ['sometimes','nullable', new TelephoneRule()],
                'adresse' => 'sometimes|required|string',
                'surname' => 'sometimes|required|string',
                'users.nom' => 'sometimes|required|string',
                'users.prenom' =>'sometimes|required|string',
                'users.login' => 'sometimes|required|string|unique:users,login',
                'users.email' => 'sometimes|required|email|unique:users,email',
                'users.password' => ['sometimes' ,'required', 'string', 'min:8', new PasswordRule()]
            ];
        }
    
        public function messages()
        {
            return [
                'users.login.unique' => 'Le login est déjà utilisé.',
                'users.email.unique' => 'L\'email est déjà utilisé.',
            ];
        }
    }
    

