<?php

namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\DTO\UserDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;


class AuthController extends Controller
{

    /**
     * Функция авторизации пользователя.
     * В случае успеха возвращает access_token пользователя в формате JSON
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request) :JsonResponse
    {
        $authDTO = $request -> toAuthDTO();
        $authDTO = $authDTO -> toArray();

        if (!Auth::attempt($authDTO)) {
            return response()->json('Неверный логин или пароль', 401);
        }

        $expiration = Config::get('sanctum.expiration');
        $maxActiveTokens = env('MAX_ACTIVE_TOKENS', 5);

        $user = Auth::user(); 

        if ($user->tokens()->count() >= $maxActiveTokens) {
            $oldestToken = $user->tokens()->oldest()->first();
            $oldestToken->delete();
        }

        $token = $user->createToken('api' , ['*'] , now() -> addMinutes($expiration));

        return response()->json(['token' => $token->plainTextToken]);

    }


    /**
     * Функция регистрации нового пользователя.
     * Возвращает экземпляр созданного пользователя в формате JSON.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request) : JsonResponse
    {
       
        $data = $request->toRegistrationDTO();
        $data = $data -> toArray();

        $user = new User();

        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->birthday = $data['birthday'];

        $user->save();

        

        return response()->json($user, 201);
    }


    /**
     * Возвращает экземпляр авторизованного пользователя в формате JSON.
     *
     * @return JsonResponse
     */
    public function me() : JsonResponse
    {
        $user = Auth::user();
        $userDTO = UserDTO::fromUser($user);
        return response()->json(['userInfo' => $userDTO->toArray()]);
    }


    /**
     * Отзывает(удаляет) текущий токен доступа авторизованного пользователя.
     *
     * @return JsonResponse
     */
    public function out() : JsonResponse
    {
        Auth::user()  -> currentAccessToken() -> delete();
        return response()->json('Пользователь был успешно разлогирован', 200);
    }

    /**
     * Получение всех токенов доступа авторизованного пользователя в формате JSON
     *
     * @return JsonResponse
     */
    public function getTokens() : JsonResponse
    {
        $user = Auth::user();
        $tokens = [];

        foreach ($user->tokens as $token) {
            $tokens[] = $token;
        }

        return response()->json($tokens);
    }

    /**
     * Отзывает(удаляет) все токены доступа авторизованного пользователя.
     *
     * @return JsonResponse
     */
    public function out_all() : JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json('Все токены пользователя были успешно отозваны', 200);
    }

}
