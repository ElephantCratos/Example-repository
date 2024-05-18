<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\DTO\RolesAndUsersDTO;

class AssignRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $role)
        {
            if ($role->permissions->contains('name', 'assign-role')){ 
                return true; 
            }
        }
        return abort(403,'assign-role permission required');
    }

    public function toRolesAndUsersDTO()
    {
        
        return new RolesAndUsersDTO(
            $this->input('rolesId')
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
            'rolesId' => ['required' , 'array'],
        ];
    }


}
