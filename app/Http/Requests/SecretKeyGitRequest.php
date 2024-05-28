<?php

namespace App\Http\Requests;

use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class SecretKeyGitRequest extends FormRequest
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
            'secret_key' => [
                'required',
                'string',
                'min:36',
                'max:36',
                function ($attribute, $value) {
                    if ($value !== env('SECRET_KEY')) {
                        abort(302, '', ['Location' => route('home')]);
                    }
                },
            ],
        ];
    }
}
