<?php
use App\Models\User;
    

    function isPermissionExistForUser(User $user, string $permission) : bool
    {
        $userRoles = $user->roles;
        foreach ($userRoles as $role)
        {
            if ($role->permissions->contains('name', $permission)) 
            {
                return true;
            }
        }
        return false;
    }

    function getArrayDiff(?array $array1, ?array $array2): array
    {
        $diff = [];

        if ($array2 === null) {
            return $diff;
        }

        $array1 = $array1 ?? [];

        foreach ($array2 as $key => $value) {
            if (!array_key_exists($key, $array1) || $array1[$key] !== $value) {
                $diff[$key] = [
                    'before' => $array1[$key] ?? null,
                    'after' => $value,
                ];
            }
        }

        return $diff;
    }
