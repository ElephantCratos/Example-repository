<?php

namespace App\Http\Controllers;

use Cookie;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\DTO\UserDTO;
use App\Http\Requests\TwoAuthCodeRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

use Illuminate\Http\Request;


class AuthController extends Controller
{

    /**
     * Функция авторизации пользователя.
     * В случае успеха возвращает access_token пользователя
     * с правами на эндпоинты двухфакторной аутентификации 
     * в формате JSON
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
        public function login(LoginRequest $request) :JsonResponse
        {
            $authDTO = $request -> toAuthDTO();
            $arrayAuthDTO = $authDTO -> toArray();

            if (!Auth::attempt($arrayAuthDTO)) {
                return response()->json('Неверный логин или пароль', 401);
            }
            
            $user = Auth::user(); 
        
            TwoFactorAuthController::sendEmailVerification($user);

            $expiration = Config::get('sanctum.expiration');
            $maxActiveTokens = env('MAX_ACTIVE_TOKENS', 5);

            $user = Auth::user(); 

                if ($user->tokens()->count() >= $maxActiveTokens) {
                    $oldestToken = $user->tokens()->oldest()->first();
                    $oldestToken->delete();
                }

            $token = $user->createToken('api' , ['confirm-2auth'] , now() -> addMinutes($expiration));

            
            return response()->json([
                'message' => 'Вам на почту было отправлено сообщение с кодом для прохождения двухфакторной аутентификации.',
                'token' => $token->plainTextToken
            ]);

        }


    /**
     * Функция подтверждения двухфакторной аутентификации.
     * В случае успеха возвращает access_token пользователя с полными правами в формате JSON
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
        public function confirmTwoAuth(TwoAuthCodeRequest $request) : JsonResponse
        {
            $code = $request->input('code');
            
            if (TwoFactorAuthController::confirmCode($code)){
                
            $expiration = Config::get('sanctum.expiration');
            $maxActiveTokens = env('MAX_ACTIVE_TOKENS', 5);

            $user = Auth::user(); 

                if ($user->tokens()->count() >= $maxActiveTokens) {
                    $oldestToken = $user->tokens()->oldest()->first();
                    $oldestToken->delete();
                }
            
            $token = $user->createToken('api' , ['use-website'] , now() -> addMinutes($expiration));
            $user->currentAccessToken()->delete();

            return response()->json(['token' => $token->plainTextToken]);
            }
            return response()->json(['message' => 'Wrong Code']);
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
