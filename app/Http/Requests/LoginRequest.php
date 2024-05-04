<?php

namespace App\Http\Requests;
use App\DTO\AuthDTO;
use App\Rules\StartsWithUppercase;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Преобразует текущий объект в AuthDTO.
     *
     * @return AuthDTO
     */
    public function toAuthDTO() : AuthDTO
    {

        return new AuthDTO(
            $this->input('username'),
            $this->input('password'),
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required','regex:/^[a-z]+$/i','min:7', new StartsWithUppercase()],
            'password' => ['required', Password::min(8) -> letters() ->  numbers() ->mixedCase()],
        ];
    }
    public function messages()
    {
        return [
            'username.required' => 'Поле "Имя пользователя" обязательно для заполнения.',
            'username.regex' => 'Поле "Имя пользователя" должно содержать только буквы латинского алфавита.',
            'username.min' => 'Поле "Имя пользователя" должно содержать минимум 7 символов.',
            'username.startsWithUppercase' => 'Поле "Имя пользователя" должно начинаться с заглавной буквы.',

            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.letters' => 'Пароль должен содержать буквы.',
            'password.numbers' => 'Пароль должен содержать цифры.',
            'password.mixedCase' => 'Пароль должен содержать символы верхнего и нижнего регистра.',
        ];
    }
}
