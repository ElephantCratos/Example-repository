<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\DTO\RolesAndPermissionsDTO;

class AssignPermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function authorize(): bool
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $role)
        {
            if ($role->permissions->contains('name', 'assign-permission')) {
                    return true;
            }
        }
        return abort(403,'assign-permission permission required');
    }

    public function toRolesAndPermissionsDTO()
    {
        return new RolesAndPermissionsDTO(
            $this->input('permissionsId'),
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
            'permissionsId' => ['required' , 'array'],
        ];
    }
}
