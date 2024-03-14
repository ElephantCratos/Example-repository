<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\StartsWithUppercase;
use Illuminate\Validation\Rules\Password;
use App\DTO\RegistrationDTO;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function authorize(): bool
    {
        return true;
    }


    /**
     * Преобразует текущий объект в RegistrationDTO.
     *
     * @return RegistrationDTO
     */
    public function toRegistrationDTO() : RegistrationDTO
    {
        return new RegistrationDTO(
            $this->input('username'),
            $this->input('email'),
            $this->input('password'),
            $this->input('c_password'),
            $this->input('birthday')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username'   => ['required' , 'regex:/^[a-z]+$/i' , 'min:7' , 'unique:users',new StartsWithUppercase()],
            'email'      => ['required', 'email', 'unique:users'],
            'password'   => ['required', Password::min(8) -> letters() ->  numbers() ->mixedCase()],
            'c_password' => ['required', 'same:password'],
            'birthday'   => ['required', 'date_format:Y-m-d'],
        ];
    }
    public function messages()
    {
        return [
            'username.required' => 'Поле "Имя пользователя" обязательно для заполнения.',
            'username.regex' => 'Поле "Имя пользователя" должно содержать только буквы латинского алфавита.',
            'username.min' => 'Поле "Имя пользователя" должно содержать минимум 7 символов.',
            'username.startsWithUppercase' => 'Поле "Имя пользователя" должно начинаться с заглавной буквы.',
            'username.unique:users' => 'Пользователь с таким именем уже существует',

            'email.required' => 'Поле "Email" обязательно для заполнения',
            'email.email' => 'Некорректный Email',
            'email.unique:users' => 'Данный Email уже зарегистрирован',

            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.letters' => 'Пароль должен содержать буквы.',
            'password.numbers' => 'Пароль должен содержать цифры.',
            'password.mixedCase' => 'Пароль должен содержать символы верхнего и нижнего регистра.',

            'c_password.required' => 'Поле "Подтвердить пароль" обязательно для заполнения',
            'c_password.same:password' => 'Пароли не совпадают',

            'birthday.required' => 'Поле "День рождения" обязательно к заполнению',
        ];
    }
}
