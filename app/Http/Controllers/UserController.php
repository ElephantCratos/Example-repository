<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\DTO\UserDTO;
use App\DTO\UsersCollectionDTO;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Возвращает коллекцию зарегистрированных пользователей в формате JSON.
     *
     * @return JsonResponse
     */
    public function showUsersList() : JsonResponse
    {
        $users = User::all();
        $usersDTO = new UsersCollectionDTO($users);
        $usersDTO = $usersDTO->getFilteredUsers();
        return response()->json(['usersList' => $usersDTO], 200);
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
}
